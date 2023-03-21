<?php

namespace App\Providers;

use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\DictionaryResource;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\UserResource;
use Illuminate\Support\ServiceProvider;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app(MoonShine::class)->registerResources([
            MenuGroup::make('System', [
                MenuItem::make('Admins', new MoonShineUserResource(), 'heroicons.users'),
                MenuItem::make('Roles', new MoonShineUserRoleResource(), 'heroicons.shield-exclamation'),
            ], 'heroicons.user-group')->canSee(static function () {
                return auth('moonshine')->user()->moonshine_user_role_id === 1;
            }),

            MenuGroup::make('Blog', [
                MenuItem::make('Categories', new CategoryResource(), 'heroicons.document'),
                MenuItem::make('Articles', new ArticleResource(), 'heroicons.newspaper'),
                MenuItem::make('Comments', new CommentResource(), 'heroicons.chat-bubble-left')
                    ->badge(fn() => Comment::query()->count()),
            ], 'heroicons.newspaper'),

            MenuItem::make('Users', new UserResource(), 'heroicons.users'),


            MenuItem::make('Dictionary', new DictionaryResource(), 'heroicons.document-duplicate'),

            MenuItem::make(
                'Documentation',
                'https://moonshine.cutcode.dev',
                'heroicons.document-duplicate'
            )->badge(static fn() => 'New design')
        ]);
    }
}
