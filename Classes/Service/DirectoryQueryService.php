<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Domain\Model\Person;
use Rozumbunch\Directory\Domain\Repository\LocationRepository;
use Rozumbunch\Directory\Domain\Repository\OrganisationRepository;
use Rozumbunch\Directory\Domain\Repository\PersonRepository;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

final class DirectoryQueryService
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly OrganisationRepository $organisationRepository,
        private readonly LocationRepository $locationRepository,
    ) {}

    public function applyPersonStorage(Site $site, int $storagePid = 0): void
    {
        $this->personRepository->setDefaultQuerySettings(
            $this->querySettingsForStorage($site, $storagePid)
        );
    }

    public function resolveDetailPid(Site $site, int $detailPid = 0): int
    {
        if ($detailPid > 0) {
            return $detailPid;
        }

        return (int)$site->getSettings()->get('directory.personDetailPid', 0);
    }

    public function resolvePersonListPid(Site $site, int $listPid = 0): int
    {
        if ($listPid > 0) {
            return $listPid;
        }

        return (int)$site->getSettings()->get('directory.personListPid', 0);
    }

    /**
     * @return QueryResultInterface<int, Person>
     */
    public function findAllPersons(Site $site, int $storagePid = 0): QueryResultInterface
    {
        return $this->findPersons($site, $storagePid, new ListFilter());
    }

    /**
     * @return QueryResultInterface<int, Person>
     */
    public function findPersons(
        Site $site,
        int $storagePid = 0,
        ListFilter $filter = new ListFilter(),
    ): QueryResultInterface {
        $this->applyPersonStorage($site, $storagePid);

        return $this->personRepository->findFiltered($filter);
    }

    /**
     * @return QueryResultInterface<int, Organisation>
     */
    public function findOrganisationsForFilter(Site $site, int $storagePid = 0): QueryResultInterface
    {
        $this->applyOrganisationStorage($site, $storagePid);

        return $this->organisationRepository->findAll();
    }

    public function applyOrganisationStorage(Site $site, int $storagePid = 0): void
    {
        $this->organisationRepository->setDefaultQuerySettings(
            $this->querySettingsForStorage($site, $storagePid)
        );
    }

    public function applyLocationStorage(Site $site, int $storagePid = 0): void
    {
        $this->locationRepository->setDefaultQuerySettings(
            $this->querySettingsForStorage($site, $storagePid)
        );
    }

    public function resolveOrganisationDetailPid(Site $site, int $detailPid = 0): int
    {
        if ($detailPid > 0) {
            return $detailPid;
        }

        return (int)$site->getSettings()->get('directory.organisationDetailPid', 0);
    }

    public function resolveOrganisationListPid(Site $site, int $listPid = 0): int
    {
        if ($listPid > 0) {
            return $listPid;
        }

        return (int)$site->getSettings()->get('directory.organisationListPid', 0);
    }

    public function resolvePersonDetailPid(Site $site, int $detailPid = 0): int
    {
        return $this->resolveDetailPid($site, $detailPid);
    }

    /**
     * @param list<string> $types
     * @return QueryResultInterface<int, Organisation>
     */
    public function findAllOrganisations(Site $site, int $storagePid = 0, array $types = []): QueryResultInterface
    {
        return $this->findOrganisations($site, $storagePid, $types);
    }

    /**
     * @param list<string> $types
     * @return QueryResultInterface<int, Organisation>
     */
    public function findOrganisations(
        Site $site,
        int $storagePid = 0,
        array $types = [],
        ListFilter $filter = new ListFilter(),
    ): QueryResultInterface {
        $this->applyOrganisationStorage($site, $storagePid);

        return $this->organisationRepository->findFiltered($types, $filter);
    }

    public function findOrganisationByUid(Site $site, int $uid, int $storagePid = 0): ?Organisation
    {
        if ($uid <= 0) {
            return null;
        }

        $this->applyOrganisationStorage($site, $storagePid);

        return $this->organisationRepository->findByUid($uid);
    }

    private function querySettingsForStorage(Site $site, int $storagePid = 0): Typo3QuerySettings
    {
        if ($storagePid <= 0) {
            $storagePid = (int)$site->getSettings()->get('directory.storagePid', 0);
        }

        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds([$storagePid > 0 ? $storagePid : -1]);

        return $querySettings;
    }
}
