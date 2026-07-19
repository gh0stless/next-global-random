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
    ],
];
