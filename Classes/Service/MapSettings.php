<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Domain\Model\Location;

final readonly class MapSettings
{
    public const KIND_LOCATIONS = 'locations';
    public const KIND_ORGANISATIONS = 'organisations';
    public const KIND_CONTACTS = 'contacts';
    public const KIND_PERSONS = 'persons';
    public const KINDS = [
        self::KIND_LOCATIONS,
        self::KIND_ORGANISATIONS,
        self::KIND_CONTACTS,
        self::KIND_PERSONS,
    ];
    public const HEIGHTS = [360, 480, 640, 800];
    public const DEFAULT_HEIGHT = 480;
    public const DEFAULT_KINDS = [
        self::KIND_LOCATIONS,
        self::KIND_ORGANISATIONS,
    ];

    /**
     * @param list<string> $markerKinds
     * @param list<string> $locationTypes
     * @param list<int> $organisationUids
     * @param list<int> $personUids
     */
    public function __construct(
        public array $markerKinds = self::DEFAULT_KINDS,
        public array $locationTypes = Location::PUBLIC_TYPES,
        public array $organisationUids = [],
        public bool $includeSubUnits = true,
        public array $personUids = [],
        public int $height = self::DEFAULT_HEIGHT,
        public bool $showLegend = true,
        public bool $showList = true,
        public bool $requireConsent = true,
        public int $storagePid = 0,
        public int $organisationDetailPid = 0,
        public int $personDetailPid = 0,
    ) {}

    public function withDetailPids(int $organisationDetailPid, int $personDetailPid): self
    {
        return new self(
            $this->markerKinds,
            $this->locationTypes,
            $this->organisationUids,
            $this->includeSubUnits,
            $this->personUids,
            $this->height,
            $this->showLegend,
            $this->showList,
            $this->requireConsent,
            $this->storagePid,
            $organisationDetailPid,
            $personDetailPid,
        );
    }

    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $persistence
     */
    public static function fromFlexForm(array $settings, array $persistence = []): self
    {
        $height = (int)($settings['height'] ?? self::DEFAULT_HEIGHT);

        return new self(
            FlexFormValues::list($settings['markerKinds'] ?? '', self::KINDS, self::DEFAULT_KINDS),
            FlexFormValues::list($settings['locationTypes'] ?? '', Location::ALL_TYPES, Location::PUBLIC_TYPES),
            FlexFormValues::uids($settings['organisations'] ?? ''),
            FlexFormValues::bool($settings['includeSubUnits'] ?? 1, true),
            FlexFormValues::uids($settings['persons'] ?? ''),
            in_array($height, self::HEIGHTS, true) ? $height : self::DEFAULT_HEIGHT,
            FlexFormValues::bool($settings['showLegend'] ?? 1, true),
            FlexFormValues::bool($settings['showList'] ?? 1, true),
            FlexFormValues::bool($settings['requireConsent'] ?? 1, true),
            (int)($persistence['storagePid'] ?? 0),
            (int)($settings['organisationDetailPid'] ?? 0),
            (int)($settings['personDetailPid'] ?? 0),
        );
    }

    public function hasKind(string $kind): bool
    {
        return in_array($kind, $this->markerKinds, true);
    }
}
