<?php

use App\MoonShine\Forms\LoginForm;
use App\MoonShine\MoonShineLayout;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Http\Middleware\Authenticate;
use MoonShine\Http\Middleware\SecurityHeadersMiddleware;
use App\MoonShine\Pages\ProfilePage;
use MoonShine\Permissions\Models\MoonshineUser;

return [
    'dir' => 'app/MoonShine',
    'namespace' => 'App\MoonShine',

    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
    'logo' => env('MOONSHINE_LOGO'),
    'logo_small' => env('MOONSHINE_LOGO_SMALL'),

    'route' => [
        'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'admin'),
        'single_page_prefix' => 'page',
        'middlewares' => [
            SecurityHeadersMiddleware::class,
        ],
        'notFoundHandler' => MoonShineNotFoundException::class,
    ],

    'use_migrations' => true,
    'use_notifications' => true,
    'use_theme_switcher' => true,

    'layout' => MoonShineLayout::class,

    'disk' => 'public',

    'forms' => [
        'login' => LoginForm::class,
    ],

    'pages' => array_filter(
        [
            'dashboard' => App\MoonShine\Pages\Dashboard::class,
            'profile' => ProfilePage::class,
        ]
    ),

    'model_resources' => [
        'default_with_import' => true,
        'default_with_export' => true,
    ],

    'auth' => [
        'enable' => true,
        'middleware' => Authenticate::class,
        'fields' => [
            'username' => 'email',
            'password' => 'password',
            'name' => 'name',
            'avatar' => 'avatar',
        ],
        'guard' => 'moonshine',
        'guards' => [
            'moonshine' => [
                'driver' => 'session',
                'provider' => 'moonshine',
            ],
        ],
        'providers' => [
            'moonshine' => [
                'driver' => 'eloquent',
                'model' => MoonshineUser::class,
            ],
        ],
    ],
    'locales' => [
        'en',
        'ru',
    ],

    'tinymce' => [
        'file_manager' => env('MOONSHINE_TINYMCE_FILE_MANAGER', ''),
        'token' => env('MOONSHINE_TINYMCE_TOKEN', ''),
        'version' => env('MOONSHINE_TINYMCE_VERSION', '6'),
    ],

    'socialite' => array_filter(
        [
            'github' => ! env('DEMO_MODE', false)
                ? '/images/icons/github-mark.svg'
                : null,
        ]
    ),
];
