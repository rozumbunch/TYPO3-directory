<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Repository;

use Rozumbunch\Directory\Domain\Model\Location;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Location>
 */
class LocationRepository extends Repository
{
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @param list<string> $types
     * @return QueryResultInterface<int, Location>
     */
    public function findByTypes(array $types): QueryResultInterface
    {
        $query = $this->createQuery();
        $types = array_values(array_filter($types, static fn (string $type): bool => $type !== ''));
        if ($types !== []) {
            $query->matching($query->in('locationType', $types));
        }

        return $query->execute();
    }
}
