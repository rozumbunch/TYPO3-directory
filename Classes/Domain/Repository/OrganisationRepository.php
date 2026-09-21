<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Repository;

use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Service\ListFilter;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Organisation>
 */
class OrganisationRepository extends Repository
{
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @param list<string> $types
     * @return QueryResultInterface<int, Organisation>
     */
    public function findAllByTypes(array $types = []): QueryResultInterface
    {
        return $this->findFiltered($types, new ListFilter());
    }

    /**
     * @param list<string> $types
     * @return QueryResultInterface<int, Organisation>
     */
    public function findFiltered(array $types, ListFilter $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = $this->constraintsForFilter($query, $types, $filter);
        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query->execute();
    }

    /**
     * @param list<int> $uids
     * @return QueryResultInterface<int, Organisation>
     */
    public function findByUids(array $uids): QueryResultInterface
    {
        $query = $this->createQuery();
        $uids = array_values(array_filter($uids, static fn (int $uid): bool => $uid > 0));
        if ($uids === []) {
            $query->matching($query->equals('uid', 0));

            return $query->execute();
        }

        $query->matching($query->in('uid', $uids));

        return $query->execute();
    }

    /**
     * @return QueryResultInterface<int, Organisation>
     */
    public function findByLocationUid(int $uid): QueryResultInterface
    {
        $query = $this->createQuery();
        if ($uid <= 0) {
            $query->matching($query->equals('uid', 0));

            return $query->execute();
        }

        $query->matching($query->equals('locations.uid', $uid));

        return $query->execute();
    }

    /**
     * @param list<string> $types
     * @return list<ConstraintInterface>
     */
    private function constraintsForFilter(
        QueryInterface $query,
        array $types,
        ListFilter $filter,
    ): array {
        $constraints = [];
        $types = array_values(array_filter($types, static fn (string $type): bool => $type !== ''));
        if ($types !== []) {
            $constraints[] = $query->in('organisationType', $types);
        }

        if ($filter->q !== '') {
            $like = $this->containsLike($filter->q);
            $constraints[] = $query->logicalOr(
                $query->like('name', $like),
                $query->like('shortName', $like),
                $query->like('identifier', $like),
            );
        }

        if ($filter->zip !== '') {
            $constraints[] = $query->like('locations.zip', $this->containsLike($filter->zip));
        }

        if ($filter->city !== '') {
            $constraints[] = $query->like('locations.city', $this->containsLike($filter->city));
        }

        return $constraints;
    }

    private function containsLike(string $value): string
    {
        return '%' . addcslashes($value, '%_\\') . '%';
    }
}
