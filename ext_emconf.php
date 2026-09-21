<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Directory',
    'description' => 'Persons, organisations and locations',
    'category' => 'plugin',
    'author' => 'Rozumbunch',
    'author_company' => 'Rozumbunch',
    'author_email' => 'contact@rozumbunch.com',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
    ],
    'autoload' => [
        'psr-4' => ['Rozumbunch\\Directory\\' => 'Classes'],
    ],
    'icon' => 'Resources/Public/Icons/Extension.svg',
];
