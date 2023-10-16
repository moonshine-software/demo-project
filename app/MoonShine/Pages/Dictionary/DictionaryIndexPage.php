<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Dictionary;

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\ID;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Pages\Crud\IndexPage;

class DictionaryIndexPage extends IndexPage
{
    protected function mainLayer(): array
    {
        return [
            Heading::make('Title'),

            ...parent::mainLayer()
        ];
    }
}
