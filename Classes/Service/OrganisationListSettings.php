<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Domain\Model\Organisation;

final readonly class OrganisationListSettings
{
    public const DISPLAY_MODES = ['compact', 'logo', 'full'];
    public const LAYOUTS = ['row', 'block-2', 'block-3'];
    public const SECTIONS = ['structure', 'locations', 'contacts'];
    public const FILTER_SEARCH = 'search';
    public const FILTER_TYPE = 'type';
    public const FILTER_ADDRESS = 'address';
    public const FILTERS = [
        self::FILTER_SEARCH,
        self::FILTER_TYPE,
        self::FILTER_ADDRESS,
    ];

    /**
     * @param list<string> $organisationTypes
     * @param list<string> $enabledFilters
     */
    public function __construct(
        public string $displayMode = 'compact',
        public string $layout = 'row',
        public int $detailPid = 0,
        public int $personDetailPid = 0,
        public int $storagePid = 0,
        public int $organisationUid = 0,
        public array $organisationTypes = Organisation::ROOT_TYPES,
        public array $enabledFilters = [],
    ) {}

    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $persistence
     */
    public static function fromFlexForm(array $settings, array $persistence = []): self
    {
        $displayMode = (string)($settings['displayMode'] ?? 'compact');
        $layout = (string)($settings['layout'] ?? 'row');

        return new self(
            in_array($displayMode, self::DISPLAY_MODES, true) ? $displayMode : 'compact',
            in_array($layout, self::LAYOUTS, true) ? $layout : 'row',
            (int)($settings['detailPid'] ?? 0),
            (int)($settings['personDetailPid'] ?? 0),
            (int)($persistence['storagePid'] ?? 0),
            (int)($settings['organisation'] ?? 0),
            FlexFormValues::list(
                $settings['organisationTypes'] ?? '',
                Organisation::ALL_TYPES,
                Organisation::ROOT_TYPES,
            ),
            FlexFormValues::list($settings['enabledFilters'] ?? '', self::FILTERS),
        );
    }

    public function showsFrontendFilter(): bool
    {
        return $this->hasAnyFilter();
    }

    public function hasFilter(string $filter): bool
    {
        return in_array($filter, $this->enabledFilters, true);
    }

    public function hasAnyFilter(): bool
    {
        return $this->enabledFilters !== [];
    }

    /**
     * @return list<string>
     */
    public function typesForFilter(ListFilter $filter): array
    {
        if ($filter->type !== '' && in_array($filter->type, $this->organisationTypes, true)) {
            return [$filter->type];
        }

        return $this->organisationTypes;
    }

    public function constrain(ListFilter $filter): ListFilter
    {
        $type = $this->hasFilter(self::FILTER_TYPE)
            && in_array($filter->type, $this->organisationTypes, true)
            ? $filter->type
            : '';

        return new ListFilter(
            $this->hasFilter(self::FILTER_SEARCH) ? $filter->q : '',
            0,
            $type,
            $this->hasFilter(self::FILTER_ADDRESS) ? $filter->zip : '',
            $this->hasFilter(self::FILTER_ADDRESS) ? $filter->city : '',
        );
    }

    public static function sanitizeSection(string $section): string
    {
        return in_array($section, self::SECTIONS, true) ? $section : 'structure';
    }
}
