<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;


use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;

#[SkipMenu]
class SettingPage extends FormPage
{
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Settings';
    }

    public function components(): array
    {
        $item = $this->getResource()->getItem();

        return [
            FormBuilder::make(
                $this->getResource()->getRoute('crud.update', $item->getKey())
            )
                ->async()
                ->fields(
                    $this->getResource()
                        ->getFormFields()
                        ->push(
                            Hidden::make('_method')->setValue('PUT')
                        )
                        ->toArray()
                )
                ->name('crud')
                ->fillCast($item, $this->getResource()->getCaster())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
        ];
    }
}
