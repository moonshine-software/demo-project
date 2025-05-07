<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Dictionary;

use App\MoonShine\Resources\DictionaryResource;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Heading;
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
            TinyMce::make('Description'),
        ];
    }

    protected function mainLayer(): array
    {
        return [
            Heading::make('Title'),

            ...parent::mainLayer()
        ];
    }
}
