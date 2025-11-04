<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Dictionary\Pages;

use App\MoonShine\Resources\Dictionary\DictionaryResource;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<DictionaryResource>
 */
class DictionaryDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Text::make('Description'),
        ];
    }
}
