<?php

namespace App\MoonShine\Resources;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Decorations\Flex;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Email;
use Leeto\MoonShine\Fields\Password;
use Leeto\MoonShine\Fields\PasswordRepeat;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Fields\ID;

class UserResource extends Resource
{
	public static string $model = User::class;

	public static string $title = 'Users';

    public string $titleField = 'name';

	public function fields(): array
	{
		return [
            Flex::make('', [
                Block::make('left', [
                    ID::make()->sortable(),
                    Text::make('Name'),
                    Email::make('E-mail', 'email'),
                ]),
                Block::make('right', [
                    Password::make('Password')
                        ->customAttributes(['autocomplete' => 'new-password'])
                        ->hideOnIndex(),

                    PasswordRepeat::make('Password repeat')
                        ->customAttributes(['autocomplete' => 'confirm-password'])
                        ->hideOnIndex(),
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
        return ['id', 'text'];
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
