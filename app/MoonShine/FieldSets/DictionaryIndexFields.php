<?php

declare(strict_types=1);

namespace App\MoonShine\FieldSets;

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Resources\Resource;

final class DictionaryIndexFields
{
    public function __invoke(Resource $resource): array
    {
        return [
            Text::make('Title')->required(),
            Text::make('Slug')->required(),
        ];
    }
}
