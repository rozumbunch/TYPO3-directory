<?php

declare(strict_types=1);

defined('TYPO3') or die();

call_user_func(static function (): void {
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['directory']
        ??= 'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.div.directory';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'PersonList',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_personlist.title',
        'directory-plugin-person-list',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_personlist.description',
        'FILE:EXT:directory/Configuration/FlexForms/PersonList.xml',
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'PersonDetail',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_persondetail.title',
        'directory-plugin-person-detail',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_persondetail.description',
        'FILE:EXT:directory/Configuration/FlexForms/PersonDetail.xml',
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'OrganisationList',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationlist.title',
        'directory-plugin-organisation-list',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationlist.description',
        'FILE:EXT:directory/Configuration/FlexForms/OrganisationList.xml',
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'OrganisationShow',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationshow.title',
        'directory-plugin-organisation-show',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationshow.description',
        'FILE:EXT:directory/Configuration/FlexForms/OrganisationShow.xml',
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'OrganisationDetail',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationdetail.title',
        'directory-plugin-organisation-detail',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_organisationdetail.description',
        'FILE:EXT:directory/Configuration/FlexForms/OrganisationDetail.xml',
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Directory',
        'Map',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_map.title',
        'directory-plugin-map',
        'directory',
        'LLL:EXT:directory/Resources/Private/Language/locallang_db.xlf:tt_content.CType.directory_map.description',
        'FILE:EXT:directory/Configuration/FlexForms/Map.xml',
    );
});
