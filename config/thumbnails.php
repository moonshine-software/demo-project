<?php

return [
    'disk' => env('FILESYSTEM_DRIVER', 'local'),

    'allowed_sizes' => ['500x300', '500x100', '1000x300'],

    'defaults' => [
        'field' => 'thumbnail',
        'dir' => 'images',
        'size' => '500x300',
        'method' => 'resize',
    ]
];
