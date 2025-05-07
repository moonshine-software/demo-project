<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Hidden;

#[SkipMenu]
class SettingPage extends FormPage
{
    public function getTitle(): string
    {
        return 'Settings';
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
                        $this->getResource()
                            ->getFormFields()
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
