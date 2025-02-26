<?php

namespace App\MoonShine\Pages;


use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Traits\WithComponentsPusher;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

#[SkipMenu]
class ProfilePage extends Page
{
    use WithComponentsPusher;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return __('moonshine::ui.profile');
    }

    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        Text::make(__('moonshine::ui.resource.name'), moonshineConfig()->getUserField('name'))
                            ->required(),

                        Text::make(__('moonshine::ui.login.username'), moonshineConfig()->getUserField('username'))
                            ->required(),

                        Image::make(__('moonshine::ui.resource.avatar'), moonshineConfig()->getUserField('avatar'))
                            ->disk(moonshineConfig()->getDisk())
                            ->options(moonshineConfig()->getDiskOptions())
                            ->dir('moonshine_users')
                            ->removable()
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),
                    ]),

                    Tab::make(__('moonshine::ui.resource.password'), [
                        Heading::make(__('moonshine::ui.resource.change_password')),

                        Password::make(__('moonshine::ui.resource.password'), moonshineConfig()->getUserField('password'))
                            ->customAttributes(['autocomplete' => 'new-password'])
                            ->eye(),

                        PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                            ->customAttributes(['autocomplete' => 'confirm-password'])
                            ->eye(),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * @throws MoonShineException
     */
    protected function components(): iterable
    {
        return [
            $this->getForm(),
            FlexibleRender::make(
                view('layouts.social-auth', [
                    'title' => 'Link account',
                ])
            ),
            ...$this->getPushedComponents(),
        ];
    }

    /**
     * @throws MoonShineException
     */
    public function getForm(): FormBuilderContract
    {
        $user = MoonShineAuth::getGuard()->user() ?? MoonShineAuth::getModel();

        if (\is_null($user)) {
            throw new MoonShineException('Model is required');
        }

        return FormBuilder::make(route('moonshine.profile.store'))
            ->async()
            ->fields($this->fields())
            ->fillCast($user, new ModelCaster($user::class))
            ->submit(__('moonshine::ui.save'), [
                'class' => 'btn-lg btn-primary',
            ]);
    }
}
