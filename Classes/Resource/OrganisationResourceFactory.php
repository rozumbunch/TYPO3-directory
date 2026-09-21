<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Resource;

use Psr\Http\Message\ServerRequestInterface;
use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Service\DirectoryLinkBuilder;

final class OrganisationResourceFactory
{
    private const ORGANIGRAM_MAX_DEPTH = 8;

    public function __construct(
        private readonly DirectoryLinkBuilder $directoryLinkBuilder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function organigram(Organisation $organisation, int $detailPid, ServerRequestInterface $request): array
    {
        return $this->organigramNode($organisation, $detailPid, $request, [], true, 0);
    }

    /**
     * @param array<int, true> $ancestors
     * @return array<string, mixed>
     */
    private function organigramNode(
        Organisation $organisation,
        int $detailPid,
        ServerRequestInterface $request,
        array $ancestors,
        bool $isCurrent,
        int $depth,
    ): array {
        $uid = (int)$organisation->getUid();
        $nextAncestors = $ancestors;
        if ($uid > 0) {
            $nextAncestors[$uid] = true;
        }

        $children = [];
        if ($depth < self::ORGANIGRAM_MAX_DEPTH) {
            foreach ($organisation->getBusinessUnits() as $unit) {
                $childUid = (int)$unit->getUid();
                if ($childUid > 0 && isset($nextAncestors[$childUid])) {
                    continue;
                }
                $children[] = $this->organigramNode(
                    $unit,
                    $detailPid,
                    $request,
                    $nextAncestors,
                    false,
                    $depth + 1,
                );
            }
        }

        return array_filter(
            [
                'uid' => $organisation->getUid(),
                'slug' => ltrim($organisation->getSlug(), '/'),
                'url' => $isCurrent ? null : $this->directoryLinkBuilder->organisationDetailUri($organisation, $detailPid, $request),
                'name' => $organisation->getName(),
                'shortName' => $organisation->getShortName(),
                'initials' => $organisation->getInitials(),
                'organisationType' => $organisation->getOrganisationType(),
                'isCurrent' => $isCurrent,
                'children' => $children,
            ],
            static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== [],
        );
    }
}
