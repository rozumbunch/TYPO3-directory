<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Psr\Http\Message\ServerRequestInterface;
use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Domain\Model\Person;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class DirectoryLinkBuilder
{
    public function personDetailUri(Person $person, int $detailPid, ServerRequestInterface $request): ?string
    {
        if ($detailPid <= 0 || $person->getUid() === null) {
            return null;
        }

        return $this->generateUri($detailPid, $request, [
            'tx_directory_persondetail' => [
                'action' => 'show',
                'controller' => 'Person',
                'person' => $person->getUid(),
            ],
        ]);
    }

    public function organisationDetailUri(
        Organisation $organisation,
        int $detailPid,
        ServerRequestInterface $request,
    ): ?string {
        if ($detailPid <= 0 || $organisation->getUid() === null) {
            return null;
        }

        $pluginArguments = [
            'action' => 'show',
            'controller' => 'Organisation',
            'organisation' => $organisation->getUid(),
        ];

        return $this->generateUri($detailPid, $request, [
            'tx_directory_organisationshow' => $pluginArguments,
        ]);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function generateUri(int $detailPid, ServerRequestInterface $request, array $parameters): ?string
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return null;
        }

        $language = $request->getAttribute('language');
        if ($language instanceof SiteLanguage) {
            $parameters['_language'] = $language;
        }

        try {
            return (string)$site->getRouter()->generateUri($detailPid, $parameters);
        } catch (\Throwable) {
            return null;
        }
    }
}
