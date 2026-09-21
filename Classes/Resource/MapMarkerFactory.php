<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Resource;

use Psr\Http\Message\ServerRequestInterface;
use Rozumbunch\Directory\Domain\Model\Location;
use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Domain\Model\Person;
use Rozumbunch\Directory\Domain\Repository\LocationRepository;
use Rozumbunch\Directory\Domain\Repository\OrganisationRepository;
use Rozumbunch\Directory\Domain\Repository\PersonRepository;
use Rozumbunch\Directory\Service\DirectoryLinkBuilder;
use Rozumbunch\Directory\Service\MapSettings;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

final class MapMarkerFactory
{
    private const TREE_MAX_DEPTH = 8;
    private const KIND_ORDER = [
        MapSettings::KIND_ORGANISATIONS,
        MapSettings::KIND_LOCATIONS,
        MapSettings::KIND_CONTACTS,
        MapSettings::KIND_PERSONS,
    ];

    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly OrganisationRepository $organisationRepository,
        private readonly PersonRepository $personRepository,
        private readonly DirectoryLinkBuilder $directoryLinkBuilder,
    ) {}

    /**
     * @return array{
     *     markers: list<array<string, mixed>>,
     *     kinds: list<string>,
     *     count: int
     * }
     */
    public function build(MapSettings $settings, ServerRequestInterface $request): array
    {
        $groups = [];
        $contactPersonUids = [];
        if ($settings->hasKind(MapSettings::KIND_LOCATIONS)) {
            $this->addLocationMarkers($groups, $settings, $request);
        }
        if ($settings->hasKind(MapSettings::KIND_ORGANISATIONS) || $settings->hasKind(MapSettings::KIND_CONTACTS)) {
            $organisations = $this->resolveOrganisations($settings);
            if ($settings->hasKind(MapSettings::KIND_ORGANISATIONS)) {
                $this->addOrganisationMarkers($groups, $organisations, $settings, $request);
            }
            if ($settings->hasKind(MapSettings::KIND_CONTACTS)) {
                $contactPersonUids = $this->addContactMarkers($groups, $organisations, $settings, $request);
            }
        }
        if ($settings->hasKind(MapSettings::KIND_PERSONS)) {
            $this->addPersonMarkers($groups, $settings, $request, $contactPersonUids);
        }

        $markers = [];
        $kinds = [];
        foreach ($groups as $group) {
            $items = array_values($group['items']);
            usort(
                $items,
                static fn (array $left, array $right): int => self::kindRank($left['kind']) <=> self::kindRank($right['kind'])
            );
            $groupKinds = [];
            foreach ($items as $item) {
                $kind = (string)$item['kind'];
                if (!in_array($kind, $groupKinds, true)) {
                    $groupKinds[] = $kind;
                }
                if (!in_array($kind, $kinds, true)) {
                    $kinds[] = $kind;
                }
            }
            $markers[] = [
                'lat' => $group['lat'],
                'lng' => $group['lng'],
                'kind' => $groupKinds[0] ?? MapSettings::KIND_LOCATIONS,
                'kinds' => $groupKinds,
                'items' => $items,
            ];
        }

        usort(
            $markers,
            static fn (array $left, array $right): int => self::kindRank($left['kind']) <=> self::kindRank($right['kind'])
        );
        usort(
            $kinds,
            static fn (string $left, string $right): int => self::kindRank($left) <=> self::kindRank($right)
        );

        return [
            'markers' => $markers,
            'kinds' => $kinds,
            'count' => array_sum(array_map(
                static fn (array $marker): int => count($marker['items']),
                $markers
            )),
        ];
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     */
    private function addLocationMarkers(array &$groups, MapSettings $settings, ServerRequestInterface $request): void
    {
        foreach ($this->locationRepository->findByTypes($settings->locationTypes) as $location) {
            if (!$location instanceof Location || !$location->hasCoordinates()) {
                continue;
            }
            $organisations = $this->organisationRepository->findByLocationUid((int)$location->getUid());
            $organisation = null;
            foreach ($organisations as $related) {
                $organisation = $related;
                break;
            }
            $this->pushItem(
                $groups,
                $location,
                'location-' . (int)$location->getUid(),
                $this->locationItem($location, $organisation, $settings, $request),
            );
        }
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     * @param list<Organisation> $organisations
     */
    private function addOrganisationMarkers(
        array &$groups,
        array $organisations,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): void {
        foreach ($organisations as $organisation) {
            foreach ($organisation->getLocations() as $location) {
                if (!$location instanceof Location || !$location->hasCoordinates()) {
                    continue;
                }
                $this->pushItem(
                    $groups,
                    $location,
                    'organisation-' . (int)$organisation->getUid() . '-' . (int)$location->getUid(),
                    $this->organisationItem($organisation, $location, $settings, $request),
                );
            }
        }
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     * @param list<Organisation> $organisations
     * @return list<int>
     */
    private function addContactMarkers(
        array &$groups,
        array $organisations,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): array {
        $seen = [];
        foreach ($organisations as $organisation) {
            foreach ($organisation->getContactPersons() as $person) {
                $uid = (int)$person->getUid();
                if ($uid <= 0 || isset($seen[$uid])) {
                    continue;
                }
                $seen[$uid] = true;
                $this->addPersonAtLocations(
                    $groups,
                    $person,
                    $organisation,
                    MapSettings::KIND_CONTACTS,
                    $settings,
                    $request,
                );
            }
        }

        return array_keys($seen);
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     * @param list<int> $excludeUids
     */
    private function addPersonMarkers(
        array &$groups,
        MapSettings $settings,
        ServerRequestInterface $request,
        array $excludeUids = [],
    ): void {
        $persons = $settings->personUids !== []
            ? $this->personRepository->findByUids($settings->personUids)
            : $this->personRepository->findAll();
        $excluded = array_fill_keys($excludeUids, true);

        foreach ($persons as $person) {
            if (!$person instanceof Person) {
                continue;
            }
            $uid = (int)$person->getUid();
            if ($uid > 0 && isset($excluded[$uid])) {
                continue;
            }
            $this->addPersonAtLocations(
                $groups,
                $person,
                $person->getOrganisation(),
                MapSettings::KIND_PERSONS,
                $settings,
                $request,
            );
        }
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     */
    private function addPersonAtLocations(
        array &$groups,
        Person $person,
        ?Organisation $organisation,
        string $kind,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): void {
        foreach ($person->getLocations() as $location) {
            if (!$location instanceof Location || !$location->hasCoordinates()) {
                continue;
            }
            $this->pushItem(
                $groups,
                $location,
                $kind . '-' . (int)$person->getUid() . '-' . (int)$location->getUid(),
                $this->personItem($person, $location, $organisation, $kind, $settings, $request),
            );
        }
    }

    /**
     * @return list<Organisation>
     */
    private function resolveOrganisations(MapSettings $settings): array
    {
        $roots = $settings->organisationUids !== []
            ? $this->organisationRepository->findByUids($settings->organisationUids)
            : $this->organisationRepository->findAll();

        $collected = [];
        foreach ($roots as $organisation) {
            if (!$organisation instanceof Organisation) {
                continue;
            }
            $this->collectOrganisation($organisation, $settings->includeSubUnits, $collected, 0);
        }

        return array_values($collected);
    }

    /**
     * @param array<int, Organisation> $collected
     */
    private function collectOrganisation(
        Organisation $organisation,
        bool $includeSubUnits,
        array &$collected,
        int $depth,
    ): void {
        $uid = (int)$organisation->getUid();
        if ($uid <= 0 || isset($collected[$uid]) || $depth > self::TREE_MAX_DEPTH) {
            return;
        }
        $collected[$uid] = $organisation;
        if (!$includeSubUnits) {
            return;
        }
        foreach ($organisation->getBusinessUnits() as $unit) {
            $this->collectOrganisation($unit, true, $collected, $depth + 1);
        }
    }

    /**
     * @param array<string, array{lat: float, lng: float, items: array<string, array<string, mixed>>}> $groups
     * @param array<string, mixed> $item
     */
    private function pushItem(array &$groups, Location $location, string $itemKey, array $item): void
    {
        $lat = (float)$location->getLatitude();
        $lng = (float)$location->getLongitude();
        $groupKey = number_format($lat, 6, '.', '') . ',' . number_format($lng, 6, '.', '');
        $groups[$groupKey] ??= [
            'lat' => $lat,
            'lng' => $lng,
            'items' => [],
        ];
        $groups[$groupKey]['items'][$itemKey] = $item;
    }

    /**
     * @return array<string, mixed>
     */
    private function locationItem(
        Location $location,
        ?Organisation $organisation,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): array {
        $url = $organisation instanceof Organisation
            ? $this->directoryLinkBuilder->organisationDetailUri($organisation, $settings->organisationDetailPid, $request)
            : null;

        return $this->item(
            MapSettings::KIND_LOCATIONS,
            $location->getName(),
            $this->locationTypeLabel($location->getLocationType()),
            $location->getFormattedAddress(),
            $location->getShortDescription() !== '' ? $location->getShortDescription() : $this->plainText($location->getOpeningHours()),
            $location->getPhone(),
            $location->getEmail(),
            $location->getWebsite(),
            $url,
            $organisation instanceof Organisation ? $organisation->getName() : '',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function organisationItem(
        Organisation $organisation,
        Location $location,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): array {
        $phone = $organisation->getPhone() !== '' ? $organisation->getPhone() : $location->getPhone();
        $email = $organisation->getEmail() !== '' ? $organisation->getEmail() : $location->getEmail();
        $website = $organisation->getWebsite() !== '' ? $organisation->getWebsite() : $location->getWebsite();

        return $this->item(
            MapSettings::KIND_ORGANISATIONS,
            $organisation->getName(),
            $this->organisationTypeLabel($organisation->getOrganisationType()),
            $location->getFormattedAddress(),
            $organisation->getShortDescription() !== '' ? $organisation->getShortDescription() : $location->getShortDescription(),
            $phone,
            $email,
            $website,
            $this->directoryLinkBuilder->organisationDetailUri($organisation, $settings->organisationDetailPid, $request),
            $location->getName() !== $organisation->getName() ? $location->getName() : '',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function personItem(
        Person $person,
        Location $location,
        ?Organisation $organisation,
        string $kind,
        MapSettings $settings,
        ServerRequestInterface $request,
    ): array {
        $subtitle = $kind === MapSettings::KIND_CONTACTS
            ? $this->translate('map.kind.contacts')
            : $person->getPosition();
        $related = '';
        if ($kind === MapSettings::KIND_CONTACTS && $organisation instanceof Organisation) {
            $related = $organisation->getName();
            if ($person->getPosition() !== '') {
                $subtitle = $person->getPosition();
            }
        } elseif ($organisation instanceof Organisation) {
            $related = $organisation->getName();
        }

        return $this->item(
            $kind,
            $person->getFullName(),
            $subtitle,
            $location->getFormattedAddress(),
            $person->getShortDescription(),
            $person->getPhone() !== '' ? $person->getPhone() : $person->getMobile(),
            $person->getEmail(),
            $person->getWebsite(),
            $this->directoryLinkBuilder->personDetailUri($person, $settings->personDetailPid, $request),
            $related,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function item(
        string $kind,
        string $title,
        string $subtitle,
        string $address,
        string $summary,
        string $phone,
        string $email,
        string $website,
        ?string $url,
        string $related,
    ): array {
        $phoneHref = $this->href($phone, 'tel');
        $emailHref = $this->href($email, 'mailto');
        $websiteHref = $this->href($website, 'https');

        return array_filter(
            [
                'kind' => $kind,
                'title' => $title,
                'subtitle' => $subtitle,
                'related' => $related,
                'address' => $address,
                'summary' => $summary,
                'phone' => $phoneHref,
                'phoneLabel' => $this->linkLabel($phone, $phoneHref),
                'email' => $emailHref,
                'emailLabel' => $this->linkLabel($email, $emailHref),
                'website' => $websiteHref,
                'websiteLabel' => $this->linkLabel($website, $websiteHref),
                'url' => $url,
            ],
            static fn (mixed $value): bool => $value !== null && $value !== '',
        );
    }

    private function href(string $value, string $scheme): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (str_contains($value, '://') || str_starts_with($value, 'tel:') || str_starts_with($value, 'mailto:')) {
            return $value;
        }
        if ($scheme === 'mailto') {
            return 'mailto:' . $value;
        }
        if ($scheme === 'tel') {
            return 'tel:' . preg_replace('/[^\d+]/', '', $value);
        }

        return 'https://' . ltrim($value, '/');
    }

    private function linkLabel(string $raw, ?string $href): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        if (preg_match('/^(mailto:|tel:)/i', $raw) === 1) {
            return substr($raw, (int)strpos($raw, ':') + 1);
        }
        if (str_starts_with((string)$href, 'https://') && str_starts_with($raw, 'https://')) {
            return preg_replace('#^https?://#', '', $raw) ?? $raw;
        }

        return $raw;
    }

    private function locationTypeLabel(string $type): string
    {
        if ($type === '') {
            return '';
        }

        return $this->translate('LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:location_type.' . $type);
    }

    private function organisationTypeLabel(string $type): string
    {
        if ($type === '') {
            return '';
        }

        return $this->translate('LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:organisation_type.' . $type);
    }

    private function plainText(string $html): string
    {
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }

    private function translate(string $key): string
    {
        $label = str_starts_with($key, 'LLL:')
            ? LocalizationUtility::translate($key)
            : LocalizationUtility::translate($key, 'Directory');

        return is_string($label) && $label !== '' ? $label : $key;
    }

    private static function kindRank(string $kind): int
    {
        $index = array_search($kind, self::KIND_ORDER, true);

        return $index === false ? 99 : $index;
    }
}
