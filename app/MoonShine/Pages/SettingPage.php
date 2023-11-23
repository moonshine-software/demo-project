<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Crud\FormPage;

class SettingPage extends FormPage
{
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title(),
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'Settings';
    }

    public function components(): array
    {
        $item = $this->getResource()->getItem();

        return [
            FormBuilder::make(
                $this->getResource()->route(
                    'crud.update',
                    $item->getKey()
                )
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
                ->fillCast($item, $this->getResource()->getModelCast())
                ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']),
        ];
    }
}
