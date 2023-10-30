<?php

namespace App\Providers;

use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\DictionaryResource;
use App\MoonShine\Resources\SettingResource;
use App\MoonShine\Resources\UserResource;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Providers\MoonShineApplicationServiceProvider;
use App\MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    protected function menu(): array
    {
        return [
            MenuGroup::make('System', [
                MenuItem::make('Settings', new SettingResource(), 'heroicons.outline.adjustments-vertical'),
                MenuItem::make('Admins', new MoonShineUserResource(), 'heroicons.outline.users'),
                MenuItem::make('Roles', new MoonShineUserRoleResource(), 'heroicons.outline.shield-exclamation'),
            ], 'heroicons.outline.user-group')->canSee(static function () {
                return auth('moonshine')->user()->moonshine_user_role_id === 1;
            }),

            MenuGroup::make('Blog', [
                MenuItem::make('Categories', new CategoryResource(), 'heroicons.outline.document'),
                MenuItem::make('Articles', new ArticleResource(), 'heroicons.outline.newspaper'),
                MenuItem::make('Comments', new CommentResource(), 'heroicons.outline.chat-bubble-left')
                    ->badge(fn () => (string) Comment::query()->count()),
            ], 'heroicons.outline.newspaper'),

            MenuItem::make('Users', new UserResource(), 'heroicons.outline.users'),


            MenuItem::make('Dictionary', new DictionaryResource(), 'heroicons.outline.document-duplicate'),

            MenuItem::make(
                'Documentation',
                'https://moonshine-laravel.com',
                'heroicons.outline.document-duplicate'
            )->badge(static fn () => 'New design'),
        ];
    }

    protected function theme(): array
    {
        return [
            'colors' => [
                'primary' => '#5190fe',
                'secondary' => '#b62982',
            ],
            'darkColors' => [
                'primary' => '#5190fe',
                'secondary' => '#b62982',
            ]
        ];
    }
}
