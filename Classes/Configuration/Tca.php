<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Configuration;

final class Tca
{
    public const LL = 'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:';

    /**
     * @return array<string, mixed>
     */
    public static function languageCtrl(): array
    {
        return [
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'translationSource' => 'l10n_source',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function enableColumns(): array
    {
        return [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function systemColumns(string $table): array
    {
        $llCore = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';

        return [
            'sys_language_uid' => [
                'exclude' => true,
                'label' => $llCore . 'LGL.language',
                'config' => [
                    'type' => 'language',
                ],
            ],
            'l10n_parent' => [
                'displayCond' => 'FIELD:sys_language_uid:>:0',
                'label' => $llCore . 'LGL.l18n_parent',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['label' => '', 'value' => 0],
                    ],
                    'foreign_table' => $table,
                    'foreign_table_where' => 'AND {#' . $table . '}.{#pid}=###CURRENT_PID### AND {#' . $table . '}.{#sys_language_uid} IN (-1,0)',
                    'default' => 0,
                ],
            ],
            'l10n_diffsource' => [
                'config' => [
                    'type' => 'passthrough',
                    'default' => '',
                ],
            ],
            'l10n_source' => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'hidden' => [
                'exclude' => true,
                'label' => $llCore . 'LGL.hidden',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'default' => 0,
                ],
            ],
            'starttime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                'config' => [
                    'type' => 'datetime',
                ],
            ],
            'endtime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                'config' => [
                    'type' => 'datetime',
                ],
            ],
            'fe_group' => [
                'exclude' => true,
                'label' => $llCore . 'LGL.fe_group',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'size' => 5,
                    'maxitems' => 20,
                    'items' => [
                        ['label' => $llCore . 'LGL.hide_at_login', 'value' => -1],
                        ['label' => $llCore . 'LGL.any_login', 'value' => -2],
                        ['label' => $llCore . 'LGL.usergroups', 'value' => '--div--'],
                    ],
                    'exclusiveKeys' => '-1,-2',
                    'foreign_table' => 'fe_groups',
                    'sortItems' => [
                        'label' => 'asc',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function palettes(): array
    {
        return [
            'language' => [
                'showitem' => 'sys_language_uid, l10n_parent',
            ],
            'access' => [
                'showitem' => 'hidden, --linebreak--, starttime, endtime, --linebreak--, fe_group',
            ],
        ];
    }

    public static function languageAndAccessTabs(): string
    {
        return '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;access,
        ';
    }

    /**
     * @param list<string> $fields
     * @return array<string, mixed>
     */
    public static function slug(array $fields, string $labelKey, bool $required = true): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => $fields,
                    'fieldSeparator' => '-',
                    'replacements' => [
                        '/' => '-',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
                'required' => $required,
            ],
        ];
    }

    /**
     * @param list<string> $allowedTypes
     * @return array<string, mixed>
     */
    public static function link(string $labelKey, array $allowedTypes = ['url', 'page']): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'link',
                'allowedTypes' => $allowedTypes,
                'appearance' => [
                    'allowedOptions' => ['title'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function email(string $labelKey): array
    {
        return self::link($labelKey, ['email']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function phone(string $labelKey): array
    {
        return self::link($labelKey, ['telephone']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function rte(string $labelKey, string $preset = 'default', int $rows = 8): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => $preset,
                'cols' => 40,
                'rows' => $rows,
            ],
        ];
    }

    /**
     * Compact RTE for palette fields that stay 50/50.
     *
     * @return array<string, mixed>
     */
    public static function rteCompact(string $labelKey): array
    {
        return self::rte($labelKey, 'directory_compact', 4);
    }

    /**
     * @return array<string, mixed>
     */
    public static function text(string $labelKey, int $rows = 5): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => $rows,
                'eval' => 'trim',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function input(string $labelKey, int $max = 255, bool $required = false, bool $excludeL10n = false): array
    {
        $field = [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'config' => [
                'type' => 'input',
                'size' => min(50, $max),
                'max' => $max,
                'eval' => 'trim',
                'required' => $required,
            ],
        ];
        if ($excludeL10n) {
            $field['l10n_mode'] = 'exclude';
        }

        return $field;
    }

    /**
     * @return array<string, mixed>
     */
    public static function file(string $labelKey, int $maxItems, string $allowed = ''): array
    {
        $config = [
            'type' => 'file',
            'maxitems' => $maxItems,
            'appearance' => [
                'createNewRelationLinkTitle' => self::LL . 'file.add',
                'showPossibleLocalizationRecords' => true,
                'showAllLocalizationLink' => true,
                'showSynchronizationLink' => true,
            ],
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ];
        if ($allowed !== '') {
            $config['allowed'] = $allowed;
        }

        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => $config,
        ];
    }

    /**
     * GPS coordinates as text: TYPO3 type=number is hardcoded to 2 decimal places.
     *
     * @return array<string, mixed>
     */
    public static function coordinate(string $labelKey, float $lower, float $upper): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 16,
                'eval' => 'trim,' . \Rozumbunch\Directory\Evaluation\CoordinateEvaluation::class,
                'is_in' => $lower . ':' . $upper,
                'nullable' => true,
                'default' => '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function country(string $labelKey, string $default = 'DE', bool $required = true): array
    {
        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'country',
                'labelField' => 'localizedOfficialName',
                'default' => $default,
                'required' => $required,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function organisationsSideBySide(
        string $labelKey,
        string $mmTable,
        string $typeConstraint = '',
        bool $excludeSelf = false
    ): array {
        $table = 'tx_directory_domain_model_organisation';
        $where = 'AND {#' . $table . '}.{#sys_language_uid} IN (-1, 0)';
        if ($typeConstraint !== '') {
            $where = 'AND {#' . $table . '}.{#organisation_type}' . $typeConstraint . ' ' . $where;
        }
        if ($excludeSelf) {
            $where .= ' AND {#' . $table . '}.{#uid}!=###THIS_UID###';
        }

        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => $table,
                'foreign_table_where' => $where,
                'MM' => $mmTable,
                'size' => 8,
                'autoSizeMax' => 16,
                'minitems' => 0,
                'maxitems' => 999,
                'enableMultiSelectFilterTextfield' => true,
                'sortItems' => [
                    'label' => 'asc',
                ],
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => true,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function personOrganisationsRelation(string $labelKey): array
    {
        return self::organisationsSideBySide(
            $labelKey,
            'tx_directory_person_organisation_mm'
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function departmentOrganisationsRelation(string $labelKey): array
    {
        return self::organisationsSideBySide(
            $labelKey,
            'tx_directory_organisation_organisation_mm',
            ' IN (\'department\',\'orgunit\')',
            true
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function locationsSideBySide(string $labelKey, string $mmTable, string $typeConstraint): array
    {
        $table = 'tx_directory_domain_model_location';

        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => $table,
                'foreign_table_where' => 'AND {#' . $table . '}.{#location_type}' . $typeConstraint . ' AND {#' . $table . '}.{#sys_language_uid} IN (-1, 0)',
                'MM' => $mmTable,
                'size' => 8,
                'autoSizeMax' => 16,
                'minitems' => 0,
                'maxitems' => 999,
                'enableMultiSelectFilterTextfield' => true,
                'sortItems' => [
                    'label' => 'asc',
                ],
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                        'options' => [
                            'table' => $table,
                            'setValue' => 'prepend',
                            'title' => self::LL . 'relation.add',
                        ],
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function privateLocationsRelation(string $labelKey): array
    {
        return self::locationsSideBySide(
            $labelKey,
            'tx_directory_person_location_mm',
            '=\'private\''
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function publicLocationsRelation(string $labelKey): array
    {
        return self::locationsSideBySide(
            $labelKey,
            'tx_directory_organisation_location_mm',
            '<>\'private\''
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function contactPersonsRelation(string $labelKey): array
    {
        $table = 'tx_directory_domain_model_person';

        return [
            'exclude' => true,
            'label' => self::LL . $labelKey,
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => $table,
                'foreign_table_where' => 'AND {#' . $table . '}.{#sys_language_uid} IN (-1, 0)',
                'MM' => 'tx_directory_organisation_person_mm',
                'size' => 8,
                'autoSizeMax' => 16,
                'minitems' => 0,
                'maxitems' => 999,
                'enableMultiSelectFilterTextfield' => true,
                'sortItems' => [
                    'label' => 'asc',
                ],
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => true,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ];
    }
}
