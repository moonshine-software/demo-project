<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\MoonShine\Resources\Setting\SettingResource;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<SettingResource>
 */
#[SkipMenu]
class SettingPage extends FormPage
{
    public function getTitle(): string
    {
        return 'Settings';
    }

    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Email::make('Email'),
            Phone::make('Phone'),
            Text::make('Copyright'),
        ];
    }

    public function components(): array
    {
        if(!$this->getResource()) {
            throw new MoonShineException('Settings resource not found');
        }

        $item = $this->getResource()->getItem();

        return [
            FormBuilder::make(
                $this->getResource()->getRoute('crud.update', $item->getKey())
            )
                ->async()
                ->fields([
                    Box::make(
                        $this->getFields()
                            ->push(
                                Hidden::make('_method')->setValue('PUT')
                            )
                            ->toArray()
                    )
                ])
                ->name('crud')
                ->fillCast($item, $this->getResource()->getCaster())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
        ];
    }
}
