<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Controller;

use Psr\Http\Message\ResponseInterface;
use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Resource\OrganisationResourceFactory;
use Rozumbunch\Directory\Service\DirectoryQueryService;
use Rozumbunch\Directory\Service\DirectorySeoWriter;
use Rozumbunch\Directory\Service\ListFilter;
use Rozumbunch\Directory\Service\OrganisationListSettings;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Page\PageInformation;

final class OrganisationController extends ActionController
{
    use ContentObjectDataViewTrait;

    public function __construct(
        private readonly DirectoryQueryService $directoryQueryService,
        private readonly OrganisationResourceFactory $organisationResourceFactory,
        private readonly DirectorySeoWriter $directorySeoWriter,
    ) {}

    public function initializeAction(): void
    {
        $site = $this->site();
        if ($site instanceof Site) {
            $this->directoryQueryService->applyOrganisationStorage($site, $this->resolveFrameworkStoragePid());
        }
    }

    public function listAction(): ResponseInterface
    {
        $site = $this->site();
        $pluginSettings = $this->pluginSettings();
        $showFilter = $pluginSettings->showsFrontendFilter();
        $filter = $pluginSettings->constrain(ListFilter::fromPluginArguments($this->request->getArguments()));
        $detailPid = $site instanceof Site
            ? $this->directoryQueryService->resolveOrganisationDetailPid($site, $pluginSettings->detailPid)
            : $pluginSettings->detailPid;
        $organisations = $site instanceof Site
            ? $this->directoryQueryService->findOrganisations(
                $site,
                $pluginSettings->storagePid,
                $pluginSettings->typesForFilter($filter),
                $filter,
            )
            : [];

        $this->addCacheTag('tx_directory_domain_model_organisation');
        $this->view->assignMultiple([
            'organisations' => $organisations,
            'resultCount' => is_countable($organisations) ? count($organisations) : 0,
            'displayMode' => $pluginSettings->displayMode,
            'layout' => $pluginSettings->layout,
            'detailPid' => $detailPid,
            'filter' => $filter,
            'typeOptions' => $pluginSettings->organisationTypes,
            'showFilter' => $showFilter,
            'showSearchFilter' => $showFilter && $pluginSettings->hasFilter(OrganisationListSettings::FILTER_SEARCH),
            'showTypeFilter' => $showFilter && $pluginSettings->hasFilter(OrganisationListSettings::FILTER_TYPE),
            'showAddressFilter' => $showFilter && $pluginSettings->hasFilter(OrganisationListSettings::FILTER_ADDRESS),
            'pluginNamespace' => $this->pluginNamespace(),
        ]);

        return $this->htmlResponse();
    }

    public function initializeShowAction(): void
    {
        $this->makeOrganisationArgumentOptional();
    }

    public function showAction(?Organisation $organisation = null): ResponseInterface
    {
        if ($organisation instanceof Organisation) {
            $this->directorySeoWriter->apply($organisation);
            $this->addCacheTag('tx_directory_domain_model_organisation_' . $organisation->getUid());
        }

        $pluginSettings = $this->pluginSettings();
        $site = $this->site();
        $personDetailPid = $site instanceof Site
            ? $this->directoryQueryService->resolvePersonDetailPid($site, $pluginSettings->personDetailPid)
            : $pluginSettings->personDetailPid;

        $this->view->assignMultiple([
            'organisation' => $organisation,
            'listPid' => $this->resolveListPid(),
            'personDetailPid' => $personDetailPid,
        ]);

        return $this->htmlResponse();
    }

    public function initializeOrganigramAction(): void
    {
        $this->makeOrganisationArgumentOptional();
    }

    public function organigramAction(?Organisation $organisation = null, string $section = 'structure'): ResponseInterface
    {
        $pluginSettings = $this->pluginSettings();
        $site = $this->site();
        if (!$organisation instanceof Organisation && $site instanceof Site) {
            $organisation = $this->directoryQueryService->findOrganisationByUid(
                $site,
                $pluginSettings->organisationUid,
                $pluginSettings->storagePid,
            );
        }

        $section = OrganisationListSettings::sanitizeSection($section);

        if ($organisation instanceof Organisation) {
            $this->directorySeoWriter->apply($organisation);
            $this->addCacheTag('tx_directory_domain_model_organisation_' . $organisation->getUid());
            $detailPid = $site instanceof Site
                ? $this->directoryQueryService->resolveOrganisationDetailPid($site, $pluginSettings->detailPid)
                : $pluginSettings->detailPid;
            if ($detailPid <= 0) {
                $detailPid = $this->currentPageId();
            }
            $personDetailPid = $site instanceof Site
                ? $this->directoryQueryService->resolvePersonDetailPid($site, $pluginSettings->personDetailPid)
                : $pluginSettings->personDetailPid;

            $this->view->assignMultiple([
                'organigram' => $this->organisationResourceFactory->organigram(
                    $organisation,
                    $detailPid,
                    $this->request,
                ),
                'detailPid' => $detailPid,
                'personDetailPid' => $personDetailPid,
            ]);
        }

        $this->view->assignMultiple([
            'organisation' => $organisation,
            'section' => $section,
        ]);

        return $this->htmlResponse();
    }

    private function pluginSettings(): OrganisationListSettings
    {
        return OrganisationListSettings::fromFlexForm(
            $this->settings,
            ['storagePid' => $this->resolveFrameworkStoragePid()],
        );
    }

    private function site(): ?Site
    {
        $site = $this->request->getAttribute('site');

        return $site instanceof Site ? $site : null;
    }

    private function currentPageId(): int
    {
        $routing = $this->request->getAttribute('routing');
        if ($routing instanceof PageArguments) {
            return $routing->getPageId();
        }

        return 0;
    }

    private function resolveListPid(): int
    {
        $listPid = (int)($this->settings['listPid'] ?? 0);
        $site = $this->site();
        if ($site instanceof Site) {
            $listPid = $this->directoryQueryService->resolveOrganisationListPid($site, $listPid);
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

    private function makeOrganisationArgumentOptional(): void
    {
        if (!$this->arguments->hasArgument('organisation')) {
            return;
        }

        $organisationArgument = $this->request->hasArgument('organisation')
            ? $this->request->getArgument('organisation')
            : null;
        if ($organisationArgument === null || $organisationArgument === '' || $organisationArgument === '0') {
            $this->arguments->getArgument('organisation')->setRequired(false);
        }
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
