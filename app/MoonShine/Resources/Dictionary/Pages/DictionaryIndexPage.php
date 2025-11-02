<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Dictionary\Pages;

use App\MoonShine\Resources\Dictionary\DictionaryResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<DictionaryResource>
 */
class DictionaryIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->updateInPopover(
                $this->getListComponentName()
            ),
            Slug::make('Slug'),
        ];
    }

    protected function mainLayer(): array
    {
        return [
            Heading::make('Custom main layer'),

            ...parent::mainLayer()
        ];
    }

    /**
     * @param TableBuilder $component
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->clickAction(ClickAction::EDIT);
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Title'),
        ];
    }
}
