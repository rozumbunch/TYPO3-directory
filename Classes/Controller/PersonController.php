<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Controller;

use Psr\Http\Message\ResponseInterface;
use Rozumbunch\Directory\Domain\Model\Person;
use Rozumbunch\Directory\Service\DirectoryQueryService;
use Rozumbunch\Directory\Service\DirectorySeoWriter;
use Rozumbunch\Directory\Service\ListFilter;
use Rozumbunch\Directory\Service\PersonListSettings;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Page\PageInformation;

final class PersonController extends ActionController
{
    public function __construct(
        private readonly DirectoryQueryService $directoryQueryService,
        private readonly DirectorySeoWriter $directorySeoWriter,
    ) {}

    public function initializeAction(): void
    {
        $site = $this->site();
        if ($site instanceof Site) {
            $this->directoryQueryService->applyPersonStorage($site, $this->resolveFrameworkStoragePid());
        }
    }

    public function listAction(): ResponseInterface
    {
        $site = $this->site();
        $pluginSettings = PersonListSettings::fromFlexForm(
            $this->settings,
            ['storagePid' => $this->resolveFrameworkStoragePid()],
        );
        $showFilter = $pluginSettings->showsFrontendFilter();
        $filter = $pluginSettings->usesBackendFilter()
            ? $pluginSettings->backendFilter
            : $pluginSettings->constrain(ListFilter::fromPluginArguments($this->request->getArguments()));
        $detailPid = $site instanceof Site
            ? $this->directoryQueryService->resolveDetailPid($site, $pluginSettings->detailPid)
            : $pluginSettings->detailPid;
        $persons = $site instanceof Site
            ? $this->directoryQueryService->findPersons($site, $pluginSettings->storagePid, $filter)
            : [];
        $organisations = $site instanceof Site && $showFilter
            && $pluginSettings->hasFilter(PersonListSettings::FILTER_ORGANISATION)
            ? $this->directoryQueryService->findOrganisationsForFilter($site, $pluginSettings->storagePid)
            : [];

        $this->addCacheTag('tx_directory_domain_model_person');
        $this->view->assignMultiple([
            'persons' => $persons,
            'resultCount' => is_countable($persons) ? count($persons) : 0,
            'displayMode' => $pluginSettings->displayMode,
            'layout' => $pluginSettings->layout,
            'detailPid' => $detailPid,
            'filter' => $filter,
            'organisations' => $organisations,
            'showFilter' => $showFilter,
            'showSearchFilter' => $showFilter && $pluginSettings->hasFilter(PersonListSettings::FILTER_SEARCH),
            'showOrganisationFilter' => $showFilter && $pluginSettings->hasFilter(PersonListSettings::FILTER_ORGANISATION),
            'showAddressFilter' => $showFilter && $pluginSettings->hasFilter(PersonListSettings::FILTER_ADDRESS),
            'pluginNamespace' => $this->pluginNamespace(),
        ]);

        return $this->htmlResponse();
    }

    public function initializeShowAction(): void
    {
        if (!$this->arguments->hasArgument('person')) {
            return;
        }

        $personArgument = $this->request->hasArgument('person')
            ? $this->request->getArgument('person')
            : null;
        if ($personArgument === null || $personArgument === '' || $personArgument === '0') {
            $this->arguments->getArgument('person')->setRequired(false);
        }
    }

    public function showAction(?Person $person = null): ResponseInterface
    {
        if ($person instanceof Person) {
            $this->directorySeoWriter->apply($person);
            $this->addCacheTag('tx_directory_domain_model_person_' . $person->getUid());
        }

        $this->view->assignMultiple([
            'person' => $person,
            'listPid' => $this->resolveListPid(),
        ]);

        return $this->htmlResponse();
    }

    private function site(): ?Site
    {
        $site = $this->request->getAttribute('site');

        return $site instanceof Site ? $site : null;
    }

    private function resolveListPid(): int
    {
        $listPid = (int)($this->settings['listPid'] ?? 0);
        $site = $this->site();
        if ($listPid <= 0 && $site instanceof Site) {
            $listPid = $this->directoryQueryService->resolvePersonListPid($site);
        }
        if ($listPid <= 0) {
            $pageInformation = $this->request->getAttribute('frontend.page.information');
            if ($pageInformation instanceof PageInformation) {
                $listPid = (int)($pageInformation->getPageRecord()['pid'] ?? 0);
            }
        }

        return max(0, $listPid);
    }

    private function pluginNamespace(): string
    {
        return 'tx_'
            . strtolower($this->request->getControllerExtensionName())
            . '_'
            . strtolower($this->request->getPluginName());
    }

    private function resolveFrameworkStoragePid(): int
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        $configuredPids = array_values(array_filter(
            GeneralUtility::intExplode(
                ',',
                (string)($frameworkConfiguration['persistence']['storagePid'] ?? ''),
                true
            )
        ));

        return $configuredPids[0] ?? 0;
    }

    private function addCacheTag(string $tag): void
    {
        $collector = $this->request->getAttribute('frontend.cache.collector');
        if ($collector === null) {
            return;
        }

        $collector->addCacheTags(new CacheTag($tag));
    }
}
