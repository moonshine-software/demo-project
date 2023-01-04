<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Date;
use Leeto\MoonShine\Fields\Email;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Password;
use Leeto\MoonShine\Fields\PasswordRepeat;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Resources\Resource;

class MoonShineUserResource extends Resource
{
    public static string $model = MoonshineUser::class;

    public string $titleField = 'name';

    public static bool $withPolicy = true;

    public function title(): string
    {
        return trans('moonshine::ui.resource.admins_title');
    }

    public function fields(): array
    {
        return [
            Tab::make('Main', [
                ID::make()
                    ->sortable()
                    ->showOnExport(),

                BelongsTo::make(
                    trans('moonshine::ui.resource.role'),
                    'moonshine_user_role_id',
                    new MoonShineUserRoleResource()
                )
                    ->showOnExport(),

                Text::make(trans('moonshine::ui.resource.name'), 'name')
                    ->required()
                    ->showOnExport(),

                Image::make(trans('moonshine::ui.resource.avatar'), 'avatar')
                    ->removable()
                    ->showOnExport()
                    ->disk('public')
                    ->dir('moonshine_users')
                    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                Date::make(trans('moonshine::ui.resource.created_at'), 'created_at')
                    ->format("d.m.Y")
                    ->default(now()->toDateTimeString())
                    ->sortable()
                    ->hideOnForm()
                    ->showOnExport(),

                Email::make(trans('moonshine::ui.resource.email'), 'email')
                    ->sortable()
                    ->showOnExport()
                    ->required(),
            ]),

            Tab::make(trans('moonshine::ui.resource.password'), [
                Heading::make('Test'),

                Password::make(trans('moonshine::ui.resource.password'), 'password')
                    ->customAttributes(['autocomplete' => 'new-password'])
                    ->hideOnIndex(),

                PasswordRepeat::make(trans('moonshine::ui.resource.repeat_password'), 'password_repeat')
                    ->customAttributes(['autocomplete' => 'confirm-password'])
                    ->hideOnIndex(),
            ]),
        ];
    }

    public function itemActions(): array
    {
        return [
            ItemAction::make('Login as', function (MoonshineUser $item) {
                auth(config('moonshine.auth.guard'))->login($item);
            }, 'Success')->icon('users')
        ];
    }

    public function rules($item): array
    {
        return [
            'name' => 'required',
            'moonshine_user_role_id' => 'required',
            'email' => 'sometimes|bail|required|email|unique:moonshine_users,email'.($item->exists ? ",$item->id" : ''),
            'password' => !$item->exists
                ? 'required|min:6|required_with:password_repeat|same:password_repeat'
                : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    public function search(): array
    {
        return ['id', 'name'];
    }

    public function filters(): array
    {
        return [
            TextFilter::make(trans('moonshine::ui.resource.name'), 'name'),
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make(trans('moonshine::ui.export')),
        ];
    }
}
