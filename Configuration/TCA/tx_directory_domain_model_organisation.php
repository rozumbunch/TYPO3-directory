<?php

declare(strict_types=1);

use Rozumbunch\Directory\Configuration\Tca;
use Rozumbunch\Directory\UserFunction\RecordLabel;

defined('TYPO3') or die();

$ll = Tca::LL;

return [
    'ctrl' => [
        'title' => $ll . 'tx_directory_domain_model_organisation',
        'label' => 'name',
        'label_alt' => 'short_name',
        'label_userFunc' => RecordLabel::class . '->getOrganisationLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'sortby' => 'sorting',
        'default_sortby' => 'name',
        'delete' => 'deleted',
        'enablecolumns' => Tca::enableColumns(),
        'typeicon_classes' => [
            'default' => 'ext-directory-organisation',
        ],
        'iconfile' => 'EXT:directory/Resources/Public/Icons/organisation.svg',
        'searchFields' => 'identifier,name,short_name,email,phone',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    name, identifier, --linebreak--, short_name, organisation_type,
                --div--;' . $ll . 'tabs.description,
                    short_description, description,
                --div--;' . $ll . 'tabs.contact,
                    --palette--;;contact,
                    contact_persons,
                --div--;' . $ll . 'tabs.business_units,
                    business_units,
                --div--;' . $ll . 'tabs.locations,
                    locations,
                --div--;' . $ll . 'tabs.media,
                    logo, media,
                --div--;' . $ll . 'tabs.seo,
                    slug, seo_title, seo_description,
                ' . Tca::languageAndAccessTabs(),
        ],
    ],
    'palettes' => array_merge(Tca::palettes(), [
        'contact' => [
            'label' => $ll . 'tabs.contact',
            'showitem' => 'email, phone, --linebreak--, website, social_profile',
        ],
    ]),
    'columns' => array_merge(Tca::systemColumns('tx_directory_domain_model_organisation'), [
        'name' => Tca::input('tx_directory_domain_model_organisation.name', 255, true),
        'identifier' => Tca::input('identifier', 255, false, true),
        'short_name' => Tca::input('tx_directory_domain_model_organisation.short_name', 100),
        'organisation_type' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_organisation.organisation_type',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => ''],
                    ['label' => $ll . 'organisation_type.department', 'value' => 'department'],
                    ['label' => $ll . 'organisation_type.authority', 'value' => 'authority'],
                    ['label' => $ll . 'organisation_type.institution', 'value' => 'institution'],
                    ['label' => $ll . 'organisation_type.orgunit', 'value' => 'orgunit'],
                    ['label' => $ll . 'organisation_type.other', 'value' => 'other'],
                    ['label' => $ll . 'organisation_type.company', 'value' => 'company'],
                    ['label' => $ll . 'organisation_type.association', 'value' => 'association'],
                ],
                'sortItems' => [
                    'label' => 'asc',
                ],
                'default' => '',
            ],
        ],
        'short_description' => Tca::text('tx_directory_domain_model_organisation.short_description', 3),
        'description' => Tca::rte('tx_directory_domain_model_organisation.description'),
        'email' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_organisation.email',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['email'],
                'appearance' => [
                    'allowedOptions' => ['title'],
                ],
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_organisation.phone',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['telephone'],
                'appearance' => [
                    'allowedOptions' => ['title'],
                ],
            ],
        ],
        'website' => Tca::link('tx_directory_domain_model_organisation.website'),
        'social_profile' => Tca::link('tx_directory_domain_model_organisation.social_profile'),
        'contact_persons' => Tca::contactPersonsRelation('tx_directory_domain_model_organisation.contact_persons'),
        'business_units' => Tca::departmentOrganisationsRelation('tx_directory_domain_model_organisation.business_units'),
        'logo' => Tca::file('tx_directory_domain_model_organisation.logo', 1, 'common-image-types'),
        'media' => Tca::file('tx_directory_domain_model_organisation.media', 99),
        'locations' => Tca::publicLocationsRelation('tx_directory_domain_model_organisation.locations'),
        'slug' => Tca::slug(['name'], 'tx_directory_domain_model_organisation.slug'),
        'seo_title' => Tca::input('tx_directory_domain_model_organisation.seo_title'),
        'seo_description' => Tca::text('tx_directory_domain_model_organisation.seo_description', 3),
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ]),
];
