<?php

declare(strict_types=1);

use Rozumbunch\Directory\Configuration\Tca;
use Rozumbunch\Directory\UserFunction\RecordLabel;

defined('TYPO3') or die();

$ll = Tca::LL;

return [
    'ctrl' => [
        'title' => $ll . 'tx_directory_domain_model_person',
        'label' => 'last_name',
        'label_alt' => 'first_name,display_name',
        'label_userFunc' => RecordLabel::class . '->getPersonLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'sortby' => 'sorting',
        'default_sortby' => 'last_name, first_name',
        'delete' => 'deleted',
        'enablecolumns' => Tca::enableColumns(),
        'typeicon_classes' => [
            'default' => 'ext-directory-person',
        ],
        'iconfile' => 'EXT:directory/Resources/Public/Icons/person.svg',
        'searchFields' => 'identifier,first_name,last_name,display_name,email,phone,mobile,position',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;name,
                --div--;' . $ll . 'tabs.contact,
                    --palette--;;contact,
                --div--;' . $ll . 'tabs.person_address,
                    locations,
                --div--;' . $ll . 'tabs.media,
                    image, media,
                --div--;' . $ll . 'tabs.work,
                    --palette--;;work,
                --div--;' . $ll . 'tabs.description,
                    short_description, description,
                --div--;' . $ll . 'tabs.seo,
                    slug, seo_title, seo_description,
                ' . Tca::languageAndAccessTabs(),
        ],
    ],
    'palettes' => array_merge(Tca::palettes(), [
        'name' => [
            'label' => $ll . 'palette.name',
            'showitem' => 'salutation, title, display_name, --linebreak--, first_name, last_name, --linebreak--, identifier',
        ],
        'contact' => [
            'label' => $ll . 'tabs.contact',
            'showitem' => 'email, phone, mobile, --linebreak--, website, social_profile',
        ],
        'work' => [
            'label' => $ll . 'tabs.work',
            'showitem' => 'position, --linebreak--, organisations',
        ],
    ]),
    'columns' => array_merge(Tca::systemColumns('tx_directory_domain_model_person'), [
        'identifier' => Tca::input('identifier', 255, false, true),
        'salutation' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_person.salutation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $ll . 'salutation.none', 'value' => ''],
                    ['label' => $ll . 'salutation.mr', 'value' => 'mr'],
                    ['label' => $ll . 'salutation.ms', 'value' => 'ms'],
                    ['label' => $ll . 'salutation.diverse', 'value' => 'diverse'],
                ],
                'default' => '',
            ],
        ],
        'first_name' => Tca::input('tx_directory_domain_model_person.first_name'),
        'last_name' => Tca::input('tx_directory_domain_model_person.last_name', 255, true),
        'title' => Tca::input('tx_directory_domain_model_person.title', 100),
        'display_name' => Tca::input('tx_directory_domain_model_person.display_name'),
        'position' => Tca::input('tx_directory_domain_model_person.position'),
        'short_description' => Tca::text('tx_directory_domain_model_person.short_description', 3),
        'description' => Tca::rte('tx_directory_domain_model_person.description'),
        'email' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_person.email',
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
            'label' => $ll . 'tx_directory_domain_model_person.phone',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['telephone'],
                'appearance' => [
                    'allowedOptions' => ['title'],
                ],
            ],
        ],
        'mobile' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_person.mobile',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'link',
                'allowedTypes' => ['telephone'],
                'appearance' => [
                    'allowedOptions' => ['title'],
                ],
            ],
        ],
        'website' => Tca::link('tx_directory_domain_model_person.website'),
        'social_profile' => Tca::link('tx_directory_domain_model_person.social_profile'),
        'image' => Tca::file('tx_directory_domain_model_person.image', 1, 'common-image-types'),
        'media' => Tca::file('tx_directory_domain_model_person.media', 99),
        'organisations' => Tca::personOrganisationsRelation('tx_directory_domain_model_person.organisations'),
        'locations' => Tca::privateLocationsRelation('tx_directory_domain_model_person.locations'),
        'slug' => Tca::slug(['last_name', 'first_name'], 'tx_directory_domain_model_person.slug'),
        'seo_title' => Tca::input('tx_directory_domain_model_person.seo_title'),
        'seo_description' => Tca::text('tx_directory_domain_model_person.seo_description', 3),
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ]),
];
