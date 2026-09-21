<?php

declare(strict_types=1);

use Rozumbunch\Directory\Domain\Model\Location;
use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Domain\Model\Person;

return [
    Organisation::class => [
        'tableName' => 'tx_directory_domain_model_organisation',
    ],
    Location::class => [
        'tableName' => 'tx_directory_domain_model_location',
    ],
    Person::class => [
        'tableName' => 'tx_directory_domain_model_person',
    ],
];
