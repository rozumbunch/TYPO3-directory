<?php

declare(strict_types=1);

use Rozumbunch\Directory\Configuration\Tca;
use Rozumbunch\Directory\UserFunction\RecordLabel;

defined('TYPO3') or die();

$ll = Tca::LL;
$languageAndAccess = Tca::languageAndAccessTabs();

$showGeneralAndAddress = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;name,
        --palette--;;type,
    --div--;' . $ll . 'tabs.address,
        --palette--;;address,
    --div--;' . $ll . 'tabs.map,
        --palette--;;coordinates,
';

$showCompanyAndOther = '
    --div--;' . $ll . 'tabs.description,
        short_description, description,
    --div--;' . $ll . 'tabs.contact,
        --palette--;;contact,
    --div--;' . $ll . 'tabs.on_site,
        --palette--;;on_site,
        directions,
    --div--;' . $ll . 'tabs.media,
        media,
    --div--;' . $ll . 'tabs.seo,
        slug, seo_title, seo_description,
';

return [
    'ctrl' => [
        'title' => $ll . 'tx_directory_domain_model_location',
        'label' => 'name',
        'label_alt' => 'street,house_number,zip,city',
        'label_userFunc' => RecordLabel::class . '->getLocationLabel',
        'type' => 'location_type',
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
            'default' => 'ext-directory-location',
        ],
        'iconfile' => 'EXT:directory/Resources/Public/Icons/location.svg',
        'searchFields' => 'identifier,name,street,house_number,zip,city,district,email,phone',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        'private' => [
            'showitem' => $showGeneralAndAddress . $languageAndAccess,
        ],
        'company' => [
            'showitem' => $showGeneralAndAddress . $showCompanyAndOther . $languageAndAccess,
        ],
        'poi' => [
            'showitem' => $showGeneralAndAddress . '
                --div--;' . $ll . 'tabs.description,
                    short_description, description,
                --div--;' . $ll . 'tabs.media,
                    media,
                --div--;' . $ll . 'tabs.seo,
                    slug, seo_title, seo_description,
            ' . $languageAndAccess,
        ],
        'other' => [
            'showitem' => $showGeneralAndAddress . $showCompanyAndOther . $languageAndAccess,
        ],
    ],
    'palettes' => array_merge(Tca::palettes(), [
        'type' => [
            'showitem' => 'location_type',
        ],
        'name' => [
            'label' => $ll . 'palette.name',
            'showitem' => 'name, identifier',
        ],
        'address' => [
            'label' => $ll . 'palette.address',
            'showitem' => 'street, house_number, --linebreak--, zip, city, --linebreak--, address_addition, post_office_box, district, --linebreak--, region, country',
        ],
        'coordinates' => [
            'label' => $ll . 'tabs.map',
            'showitem' => 'latitude, longitude',
        ],
        'contact' => [
            'label' => $ll . 'tabs.contact',
            'showitem' => 'email, phone, --linebreak--, website',
        ],
        'on_site' => [
            'label' => $ll . 'tabs.on_site',
            'showitem' => 'opening_hours, special_hours_note, --linebreak--, parking, accessibility',
        ],
    ]),
    'columns' => array_merge(Tca::systemColumns('tx_directory_domain_model_location'), [
        'location_type' => [
            'exclude' => true,
            'label' => $ll . 'tx_directory_domain_model_location.location_type',
            'l10n_mode' => 'exclude',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $ll . 'location_type.private', 'value' => 'private'],
                    ['label' => $ll . 'location_type.company', 'value' => 'company'],
                    ['label' => $ll . 'location_type.poi', 'value' => 'poi'],
                    ['label' => $ll . 'location_type.other', 'value' => 'other'],
                ],
                'default' => 'private',
            ],
        ],
        'name' => Tca::input('tx_directory_domain_model_location.name', 255, true),
        'identifier' => Tca::input('identifier', 255, false, true),
        'street' => Tca::input('tx_directory_domain_model_location.street', 255, false, true),
        'house_number' => Tca::input('tx_directory_domain_model_location.house_number', 50, false, true),
        'address_addition' => Tca::input('tx_directory_domain_model_location.address_addition', 255, false, true),
        'post_office_box' => Tca::input('tx_directory_domain_model_location.post_office_box', 100, false, true),
        'zip' => Tca::input('tx_directory_domain_model_location.zip', 20, false, true),
        'city' => Tca::input('tx_directory_domain_model_location.city', 255, false, true),
        'district' => Tca::input('tx_directory_domain_model_location.district', 255, false, true),
        'region' => Tca::input('tx_directory_domain_model_location.region', 255, false, true),
        'country' => Tca::country('tx_directory_domain_model_location.country', 'DE', false),
        'short_description' => Tca::text('tx_directory_domain_model_location.short_description', 3),
        'description' => Tca::rte('tx_directory_domain_model_location.description'),
        'email' => Tca::email('tx_directory_domain_model_location.email'),
        'phone' => Tca::phone('tx_directory_domain_model_location.phone'),
        'website' => Tca::link('tx_directory_domain_model_location.website'),
        'opening_hours' => Tca::rteCompact('tx_directory_domain_model_location.opening_hours'),
        'special_hours_note' => Tca::rteCompact('tx_directory_domain_model_location.special_hours_note'),
        'directions' => Tca::rte('tx_directory_domain_model_location.directions'),
        'parking' => Tca::rteCompact('tx_directory_domain_model_location.parking'),
        'accessibility' => Tca::rteCompact('tx_directory_domain_model_location.accessibility'),
        'latitude' => Tca::coordinate('tx_directory_domain_model_location.latitude', -90.0, 90.0),
        'longitude' => Tca::coordinate('tx_directory_domain_model_location.longitude', -180.0, 180.0),
        'media' => Tca::file('tx_directory_domain_model_location.media', 99),
        'slug' => Tca::slug(['name', 'city'], 'tx_directory_domain_model_location.slug', false),
        'seo_title' => Tca::input('tx_directory_domain_model_location.seo_title'),
        'seo_description' => Tca::text('tx_directory_domain_model_location.seo_description', 3),
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ]),
];
