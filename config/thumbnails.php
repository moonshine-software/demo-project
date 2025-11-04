<?php

return [
    'disk' => env('FILESYSTEM_DRIVER', 'public'),

    'allowed_sizes' => [
        '1000x500',
    ],

    'defaults' => [
        'field' => 'photo',
        'dir' => 'images',
        'size' => '150x150',
        'method' => 'resize',
    ]
];
