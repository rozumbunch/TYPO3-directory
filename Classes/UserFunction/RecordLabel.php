<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\UserFunction;

use Rozumbunch\Directory\Configuration\Tca;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class RecordLabel
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getPersonLabel(array &$parameters, ?object $parentObject = null): void
    {
        $row = $parameters['row'] ?? [];
        $displayName = self::firstString($row['display_name'] ?? '');
        if ($displayName !== '') {
            $parameters['title'] = $displayName;
            return;
        }

        $parts = array_filter([
            self::firstString($row['title'] ?? ''),
            self::firstString($row['first_name'] ?? ''),
            self::firstString($row['last_name'] ?? ''),
        ]);

        $parameters['title'] = implode(' ', $parts);
        if ($parameters['title'] === '') {
            $parameters['title'] = '[' . (string)($row['uid'] ?? '0') . ']';
        }
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getLocationLabel(array &$parameters, ?object $parentObject = null): void
    {
        $row = $parameters['row'] ?? [];
        $name = self::firstString($row['name'] ?? '');
        if ($name !== '') {
            $parameters['title'] = $name;
            return;
        }

        $street = trim(implode(' ', array_filter([
            self::firstString($row['street'] ?? ''),
            self::firstString($row['house_number'] ?? ''),
        ])));
        $city = trim(implode(' ', array_filter([
            self::firstString($row['zip'] ?? ''),
            self::firstString($row['city'] ?? ''),
        ])));

        $parameters['title'] = implode(', ', array_filter([$street, $city]));
        if ($parameters['title'] === '') {
            $parameters['title'] = '[' . (string)($row['uid'] ?? '0') . ']';
        }
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getOrganisationLabel(array &$parameters, ?object $parentObject = null): void
    {
        $row = $parameters['row'] ?? [];
        $name = self::firstString($row['name'] ?? '');
        if ($name === '') {
            $name = self::firstString($row['short_name'] ?? '');
        }

        if ($name === '') {
            $parameters['title'] = '[' . (string)($row['uid'] ?? '0') . ']';
            return;
        }

        $type = self::firstString($row['organisation_type'] ?? '');
        if (in_array($type, ['department', 'orgunit'], true)) {
            $suffix = $this->translate('organisation_type.' . $type);
            if ($suffix !== '') {
                $name .= ' (' . $suffix . ')';
            }
        }

        $parameters['title'] = $name;
    }

    private function translate(string $key): string
    {
        $label = trim($this->languageService()->sL(Tca::LL . $key));
        if ($label === '' || str_starts_with($label, 'LLL:')) {
            return '';
        }

        return $label;
    }

    private function languageService(): LanguageService
    {
        $languageService = $GLOBALS['LANG'] ?? null;
        if ($languageService instanceof LanguageService) {
            return $languageService;
        }

        $factory = GeneralUtility::makeInstance(LanguageServiceFactory::class);
        $backendUser = $GLOBALS['BE_USER'] ?? null;

        return $factory->createFromUserPreferences(
            $backendUser instanceof BackendUserAuthentication ? $backendUser : null
        );
    }

    private static function firstString(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return trim((string)$value);
    }
}
