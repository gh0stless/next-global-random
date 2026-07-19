<?php

declare(strict_types=1);

return [
    'routes' => [
        [
            'name' => 'page#index',
            'url' => '/',
            'verb' => 'GET',
        ],
        [
            'name' => 'page#globalRandom',
            'url' => '/app',
            'verb' => 'GET',
        ],
        [
            'name' => 'page#description',
            'url' => '/description.html',
            'verb' => 'GET',
        ],
        [
            'name' => 'page#beschreibung',
            'url' => '/beschreibung.html',
            'verb' => 'GET',
        ],
    ],
];
