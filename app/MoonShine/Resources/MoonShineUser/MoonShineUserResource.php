<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MoonShineUser;

use MoonShine\Laravel\Resources\ModelResource;
use App\MoonShine\Resources\MoonShineUser\Pages\MoonShineUserFormPage;
use App\MoonShine\Resources\MoonShineUser\Pages\MoonShineUserIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Permissions\Models\MoonshineUser;
use MoonShine\Permissions\Traits\WithPermissions;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<MoonshineUser, MoonShineUserIndexPage, MoonShineUserFormPage, null>
 */
#[Icon('users')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(1)]
class MoonShineUserResource extends ModelResource
{
    use WithPermissions;

    protected string $model = MoonshineUser::class;

    protected string $column = 'name';

    protected array $with = ['moonshineUserRole'];

    protected bool $simplePaginate = true;

    protected bool $withPolicy = true;

    public function getTitle(): string
    {
        return __('moonshine::ui.resource.admins_title');
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            MoonShineUserIndexPage::class,
            MoonShineUserFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
