<?php

declare(strict_types=1);

namespace App\MoonShine\Forms;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Password;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;

final class LoginForm
{
    public function __invoke(): FormBuilder
    {
        return FormBuilder::make()
            ->customAttributes([
                'class' => 'authentication-form',
            ])
            ->action(route('moonshine.authenticate'))
            ->fields([
                Text::make(__('moonshine::ui.login.username'), 'username')
                    ->required()
                    ->customAttributes([
                        'autofocus' => true,
                        'autocomplete' => 'username',
                    ])
                    ->default('admin@moonshine-laravel.com'),

                Password::make(__('moonshine::ui.login.password'), 'password')
                    ->required()
                    ->customAttributes([
                        'value' => 'moonshine'
                    ]),

                Switcher::make(__('moonshine::ui.login.remember_me'), 'remember'),
            ])->submit(__('moonshine::ui.login.login'), [
                'class' => 'btn-primary btn-lg w-full',
            ]);
    }
}
