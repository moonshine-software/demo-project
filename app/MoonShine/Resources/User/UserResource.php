<?php

namespace App\MoonShine\Resources\User;

use App\Models\User;
use App\MoonShine\Resources\User\Pages\UserDetailPage;
use App\MoonShine\Resources\User\Pages\UserFormPage;
use App\MoonShine\Resources\User\Pages\UserIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<User, UserIndexPage, UserFormPage, UserDetailPage>
 */
#[Icon('user')]
#[Order(4)]
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Users';

    protected string $column = 'name';

    protected function pages(): array
    {
        return [
            UserIndexPage::class,
            UserDetailPage::class,
            UserFormPage::class,
        ];
    }
}
