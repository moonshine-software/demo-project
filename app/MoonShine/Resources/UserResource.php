<?php

namespace App\MoonShine\Resources;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Email;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Text;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;

class UserResource extends Resource
{
	public static string $model = User::class;

	public static string $title = 'Users';

    public string $titleField = 'name';

	public function fields(): array
	{
		return [
            Grid::make([
                Column::make([
                    Block::make('Contact information', [
                        ID::make()->sortable(),
                        Text::make('Name'),
                        Email::make('E-mail', 'email'),
                    ]),

                    Block::make('Change password', [
                        Password::make('Password')
                            ->customAttributes(['autocomplete' => 'new-password'])
                            ->hideOnIndex(),

                        PasswordRepeat::make('Password repeat')
                            ->customAttributes(['autocomplete' => 'confirm-password'])
                            ->hideOnIndex(),
                    ])
                ])
            ])
        ];
	}

	public function rules(Model $item): array
	{
        return [
            'name' => 'required',
            'email' => 'sometimes|bail|required|email|unique:users,email'.($item->exists ? ",$item->id" : ''),
            'password' => !$item->exists
                ? 'required|min:6|required_with:password_repeat|same:password_repeat'
                : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [];
    }
}
