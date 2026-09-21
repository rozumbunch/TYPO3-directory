<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

final readonly class PersonListSettings
{
    public const DISPLAY_MODES = ['compact', 'photo', 'full'];
    public const LAYOUTS = ['row', 'block-2', 'block-3'];
    public const FILTER_MODE_FRONTEND = 'frontend';
    public const FILTER_MODE_BACKEND = 'backend';
    public const FILTER_MODES = [
        self::FILTER_MODE_FRONTEND,
        self::FILTER_MODE_BACKEND,
    ];
    public const FILTER_SEARCH = 'search';
    public const FILTER_ORGANISATION = 'organisation';
    public const FILTER_ADDRESS = 'address';
    public const FILTERS = [
        self::FILTER_SEARCH,
        self::FILTER_ORGANISATION,
        self::FILTER_ADDRESS,
    ];

    /**
     * @param list<string> $enabledFilters
     */
    public function __construct(
        public string $displayMode = 'compact',
        public string $layout = 'row',
        public int $detailPid = 0,
        public int $storagePid = 0,
        public string $filterMode = self::FILTER_MODE_FRONTEND,
        public array $enabledFilters = [],
        public ListFilter $backendFilter = new ListFilter(),
    ) {}

    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $persistence
     */
    public static function fromFlexForm(array $settings, array $persistence = []): self
    {
        $displayMode = (string)($settings['displayMode'] ?? 'compact');
        $layout = (string)($settings['layout'] ?? 'row');
        $filterMode = (string)($settings['filterMode'] ?? self::FILTER_MODE_FRONTEND);

        return new self(
            in_array($displayMode, self::DISPLAY_MODES, true) ? $displayMode : 'compact',
            in_array($layout, self::LAYOUTS, true) ? $layout : 'row',
            (int)($settings['detailPid'] ?? 0),
            (int)($persistence['storagePid'] ?? 0),
            in_array($filterMode, self::FILTER_MODES, true) ? $filterMode : self::FILTER_MODE_FRONTEND,
            FlexFormValues::list($settings['enabledFilters'] ?? '', self::FILTERS),
            ListFilter::fromBackendSettings($settings),
        );
    }

    public function showsFrontendFilter(): bool
    {
        return $this->filterMode === self::FILTER_MODE_FRONTEND && $this->hasAnyFilter();
    }

    public function usesBackendFilter(): bool
    {
        return $this->filterMode === self::FILTER_MODE_BACKEND;
    }

    public function hasFilter(string $filter): bool
    {
        return in_array($filter, $this->enabledFilters, true);
    }

    public function hasAnyFilter(): bool
    {
        return $this->enabledFilters !== [];
    }

    public function constrain(ListFilter $filter): ListFilter
    {
        return new ListFilter(
            $this->hasFilter(self::FILTER_SEARCH) ? $filter->q : '',
            $this->hasFilter(self::FILTER_ORGANISATION) ? $filter->organisation : 0,
            '',
            $this->hasFilter(self::FILTER_ADDRESS) ? $filter->zip : '',
            $this->hasFilter(self::FILTER_ADDRESS) ? $filter->city : '',
        );
    }
}
