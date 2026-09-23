<?php

declare(strict_types=1);

return [
    'default' => 'workspace',
    'connections' => [
        'registry' => [
            'driver' => 'sqlite',
            'database' => env('DEMO_REGISTRY_DATABASE', storage_path('app/demo/registry.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => true,
            'busy_timeout' => 5000,
            'journal_mode' => 'WAL',
        ],
        'workspace' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
            'busy_timeout' => 5000,
        ],
    ],
    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true],
];
