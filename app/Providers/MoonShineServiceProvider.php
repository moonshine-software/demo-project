<?php

namespace App\Providers;

use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\DictionaryResource;
use App\MoonShine\Resources\UserResource;
use Illuminate\Support\ServiceProvider;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app(MoonShine::class)->registerResources([
            MenuGroup::make('System', [
                MenuItem::make('Admins', new \App\MoonShine\Resources\MoonShineUserResource()),
                MenuItem::make('Roles', new \App\MoonShine\Resources\MoonShineUserRoleResource()),
            ], 'users')->canSee(static function () {
                return auth('moonshine')->user()->moonshine_user_role_id === 1;
            }),

            MenuGroup::make('Blog', [
                MenuItem::make('Categories', new CategoryResource()),
                MenuItem::make('Articles', new ArticleResource()),
                MenuItem::make('Comments', new CommentResource())
                    ->badge(fn() => Comment::query()->count()),
            ], 'bookmark'),

            MenuItem::make('Users', new UserResource(), 'users'),


            MenuItem::make('Dictionary', new DictionaryResource(), 'clip')
        ]);
    }
}
