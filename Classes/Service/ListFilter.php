<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Domain\Model\Organisation;

final readonly class ListFilter
{
    public function __construct(
        public string $q = '',
        public int $organisation = 0,
        public string $type = '',
        public string $zip = '',
        public string $city = '',
    ) {}

    /**
     * @param array<string, mixed> $arguments
     */
    public static function fromPluginArguments(array $arguments): self
    {
        $type = FlexFormValues::text($arguments['type'] ?? '');

        return new self(
            FlexFormValues::text($arguments['q'] ?? ''),
            FlexFormValues::uid($arguments['organisation'] ?? 0),
            in_array($type, Organisation::ALL_TYPES, true) ? $type : '',
            FlexFormValues::text($arguments['zip'] ?? ''),
            FlexFormValues::text($arguments['city'] ?? ''),
        );
    }

    /**
     * @param array<string, mixed> $settings
     */
    public static function fromBackendSettings(array $settings): self
    {
        return new self(
            FlexFormValues::text($settings['filterQ'] ?? ''),
            FlexFormValues::uid($settings['filterOrganisation'] ?? 0),
            '',
            FlexFormValues::text($settings['filterZip'] ?? ''),
            FlexFormValues::text($settings['filterCity'] ?? ''),
        );
    }

    public function isActive(): bool
    {
        return $this->q !== '' || $this->organisation > 0 || $this->type !== '' || $this->zip !== '' || $this->city !== '';
    }
}
