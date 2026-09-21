<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\RecordListController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

final class DirectoryModuleController
{
    public function __construct(
        private readonly RecordListController $recordListController,
        private readonly SiteFinder $siteFinder,
        private readonly UriBuilder $uriBuilder,
    ) {}

    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $id = (int)($queryParams['id'] ?? 0);
        $storagePid = $this->resolveStoragePid($id);

        if ($id === 0 && $storagePid > 0) {
            return new RedirectResponse(
                (string)$this->uriBuilder->buildUriFromRoute(
                    'web_directory',
                    array_replace($queryParams, ['id' => $storagePid])
                )
            );
        }

        return $this->recordListController->mainAction($request);
    }

    private function resolveStoragePid(int $pageId): int
    {
        $site = $this->findSite($pageId);
        if (!$site instanceof Site) {
            return 0;
        }

        return (int)$site->getSettings()->get('directory.storagePid', 0);
    }

    private function findSite(int $pageId): ?Site
    {
        if ($pageId > 0) {
            try {
                return $this->siteFinder->getSiteByPageId($pageId);
            } catch (\Throwable) {
                return null;
            }
        }

        $sites = $this->siteFinder->getAllSites();
        if (count($sites) !== 1) {
            return null;
        }

        return reset($sites) ?: null;
    }
}
