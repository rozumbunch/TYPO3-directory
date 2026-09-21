<?php

declare(strict_types=1);

use Rozumbunch\Directory\Controller\DirectoryModuleController;

defined('TYPO3') or die();

return [
    'web_directory' => [
        'parent' => 'web',
        'position' => ['after' => 'web_list'],
        'access' => 'user',
        'hideInMenu' => true,
        'path' => '/module/web/directory',
        'iconIdentifier' => 'ext-directory-module',
        'labels' => 'LLL:EXT:directory/Resources/Private/Language/locallang_mod.xlf',
        'navigationComponent' => '@typo3/backend/tree/page-tree-element',
        'routes' => [
            '_default' => [
                'target' => DirectoryModuleController::class . '::mainAction',
            ],
        ],
        'moduleData' => [
            'clipBoard' => true,
            'searchBox' => true,
        ],
    ],
];
