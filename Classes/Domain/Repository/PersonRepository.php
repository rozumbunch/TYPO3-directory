<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Repository;

use Rozumbunch\Directory\Domain\Model\Person;
use Rozumbunch\Directory\Service\ListFilter;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Person>
 */
class PersonRepository extends Repository
{
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
        'lastName' => QueryInterface::ORDER_ASCENDING,
        'firstName' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return QueryResultInterface<int, Person>
     */
    public function findFiltered(ListFilter $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = $this->constraintsForFilter($query, $filter);
        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query->execute();
    }

    /**
     * @param list<int> $uids
     * @return QueryResultInterface<int, Person>
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
     * @return list<ConstraintInterface>
     */
    private function constraintsForFilter(QueryInterface $query, ListFilter $filter): array
    {
        $constraints = [];

        if ($filter->q !== '') {
            $like = $this->containsLike($filter->q);
            $constraints[] = $query->logicalOr(
                $query->like('lastName', $like),
                $query->like('firstName', $like),
                $query->like('displayName', $like),
                $query->like('position', $like),
            );
        }

        if ($filter->organisation > 0) {
            $constraints[] = $query->equals('organisations.uid', $filter->organisation);
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
