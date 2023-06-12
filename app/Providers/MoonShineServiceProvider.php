<?php

namespace App\Providers;

use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\DictionaryResource;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\SettingResource;
use App\MoonShine\Resources\UserResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\MoonShine;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app(MoonShine::class)->menu([
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
                    ->badge(fn() => Comment::query()->count()),
            ], 'heroicons.outline.newspaper'),

            MenuItem::make('Users', new UserResource(), 'heroicons.outline.users'),


            MenuItem::make('Dictionary', new DictionaryResource(), 'heroicons.outline.document-duplicate'),

            MenuItem::make(
                'Documentation',
                'https://moonshine.cutcode.dev',
                'heroicons.outline.document-duplicate'
            )->badge(static fn() => 'New design')
        ]);
    }
}
