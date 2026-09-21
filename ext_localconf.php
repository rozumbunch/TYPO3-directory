<?php

declare(strict_types=1);

use Rozumbunch\Directory\Controller\MapController;
use Rozumbunch\Directory\Controller\OrganisationController;
use Rozumbunch\Directory\Controller\PersonController;
use Rozumbunch\Directory\Form\ApplyLocationTypeFromParentFormDataProvider;
use Rozumbunch\Directory\Form\ApplyStoragePidFormDataProvider;
use Rozumbunch\Directory\PageTitle\DirectoryPageTitleProvider;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordOverrideValues;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue;
use TYPO3\CMS\Backend\Form\FormDataProvider\ReturnUrl;
use TYPO3\CMS\Backend\Form\FormDataProvider\SiteResolving;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'Directory',
    'PersonList',
    [PersonController::class => 'list'],
    [PersonController::class => 'list'],
);

ExtensionUtility::configurePlugin(
    'Directory',
    'PersonDetail',
    [PersonController::class => 'show'],
);

ExtensionUtility::configurePlugin(
    'Directory',
    'OrganisationList',
    [OrganisationController::class => 'list'],
    [OrganisationController::class => 'list'],
);

ExtensionUtility::configurePlugin(
    'Directory',
    'OrganisationShow',
    [OrganisationController::class => 'show'],
);

ExtensionUtility::configurePlugin(
    'Directory',
    'OrganisationDetail',
    [OrganisationController::class => 'organigram'],
);

ExtensionUtility::configurePlugin(
    'Directory',
    'Map',
    [MapController::class => 'show'],
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][ApplyStoragePidFormDataProvider::class] = [
    'depends' => [
        SiteResolving::class,
    ],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][ApplyLocationTypeFromParentFormDataProvider::class] = [
    'depends' => [
        ReturnUrl::class,
        DatabaseRecordOverrideValues::class,
    ],
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][DatabaseRecordTypeValue::class]['depends'][] =
    ApplyLocationTypeFromParentFormDataProvider::class;

$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['directory_compact'] =
    'EXT:directory/Configuration/RTE/Compact.yaml';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Rozumbunch\Directory\Evaluation\CoordinateEvaluation::class] = '';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
    \Rozumbunch\Directory\Hook\CoordinateValueHook::class;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['frontend']['pageTitleProviders']['directory'] = [
    'provider' => DirectoryPageTitleProvider::class,
    'before' => ['altPageTitle', 'record', 'seo'],
];

$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^tx_directory_personlist';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^tx_directory_organisationlist';
