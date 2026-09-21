<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Controller;

use Psr\Http\Message\ResponseInterface;
use Rozumbunch\Directory\Resource\MapMarkerFactory;
use Rozumbunch\Directory\Service\DirectoryQueryService;
use Rozumbunch\Directory\Service\MapSettings;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class MapController extends ActionController
{
    public function __construct(
        private readonly DirectoryQueryService $directoryQueryService,
        private readonly MapMarkerFactory $mapMarkerFactory,
    ) {}

    public function initializeAction(): void
    {
        $site = $this->site();
        if (!$site instanceof Site) {
            return;
        }

        $storagePid = $this->resolveFrameworkStoragePid();
        $this->directoryQueryService->applyLocationStorage($site, $storagePid);
        $this->directoryQueryService->applyOrganisationStorage($site, $storagePid);
        $this->directoryQueryService->applyPersonStorage($site, $storagePid);
    }

    public function showAction(): ResponseInterface
    {
        $site = $this->site();
        $pluginSettings = $this->pluginSettings();
        if ($site instanceof Site) {
            $pluginSettings = $pluginSettings->withDetailPids(
                $this->directoryQueryService->resolveOrganisationDetailPid($site, $pluginSettings->organisationDetailPid),
                $this->directoryQueryService->resolvePersonDetailPid($site, $pluginSettings->personDetailPid),
            );
        }

        $map = $this->mapMarkerFactory->build($pluginSettings, $this->request);
        $contentUid = $this->contentUid();
        $config = [
            'contentUid' => $contentUid,
            'height' => $pluginSettings->height,
            'requireConsent' => $pluginSettings->requireConsent,
            'tileUrl' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'tileAttribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            'markers' => $map['markers'],
            'labels' => [
                'details' => $this->translate('map.details'),
                'route' => $this->translate('map.route'),
                'phone' => $this->translate('label.phone'),
                'email' => $this->translate('label.email'),
                'website' => $this->translate('label.website'),
                'address' => $this->translate('label.address'),
                'kind.locations' => $this->translate('map.kind.locations'),
                'kind.organisations' => $this->translate('map.kind.organisations'),
                'kind.contacts' => $this->translate('map.kind.contacts'),
                'kind.persons' => $this->translate('map.kind.persons'),
            ],
        ];

        $this->addCacheTag('tx_directory_domain_model_location');
        $this->addCacheTag('tx_directory_domain_model_organisation');
        $this->addCacheTag('tx_directory_domain_model_person');
        $this->view->assignMultiple([
            'contentUid' => $contentUid,
            'height' => $pluginSettings->height,
            'showLegend' => $pluginSettings->showLegend,
            'showList' => $pluginSettings->showList,
            'requireConsent' => $pluginSettings->requireConsent,
            'markers' => $map['markers'],
            'kinds' => $map['kinds'],
            'resultCount' => $map['count'],
            'mapConfigJson' => json_encode(
                $config,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ),
        ]);

        return $this->htmlResponse();
    }

    private function pluginSettings(): MapSettings
    {
        return MapSettings::fromFlexForm(
            $this->settings,
            ['storagePid' => $this->resolveFrameworkStoragePid()],
        );
    }

    private function site(): ?Site
    {
        $site = $this->request->getAttribute('site');

        return $site instanceof Site ? $site : null;
    }

    private function contentUid(): int
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        if ($contentObject instanceof ContentObjectRenderer) {
            return (int)($contentObject->data['uid'] ?? 0);
        }

        return 0;
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

    private function translate(string $key): string
    {
        $label = LocalizationUtility::translate($key, 'Directory');

        return is_string($label) && $label !== '' ? $label : $key;
    }
}
