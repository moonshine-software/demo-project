<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Dictionary;

use App\MoonShine\Resources\DictionaryResource;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\TinyMce\Fields\TinyMce;
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
            TinyMce::make('Description'),
        ];
    }
}
