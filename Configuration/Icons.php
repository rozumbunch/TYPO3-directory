<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-directory-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/module.svg',
    ],
    'ext-directory-person' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/person.svg',
    ],
    'directory-plugin-person-list' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/person.svg',
    ],
    'directory-plugin-person-detail' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/person.svg',
    ],
    'ext-directory-organisation' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/organisation.svg',
    ],
    'directory-plugin-organisation-list' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/organisation.svg',
    ],
    'directory-plugin-organisation-show' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/organisation.svg',
    ],
    'directory-plugin-organisation-detail' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/organisation.svg',
    ],
    'ext-directory-location' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/location.svg',
    ],
    'directory-plugin-map' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:directory/Resources/Public/Icons/location.svg',
    ],
];
