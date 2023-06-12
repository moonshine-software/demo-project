<?php

declare(strict_types=1);

namespace App\MoonShine\FieldSets;

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Resources\Resource;

final class DictionaryFormFields
{
    public function __invoke(Resource $resource): array
    {
        return [
            Block::make('', [
                ID::make()->sortable(),
                Text::make('Title')->required(),
                Slug::make('Slug')
                    ->unique()
                    ->separator('-')
                    ->from('title')
                    ->required(),
                TinyMce::make('Description'),
            ])
        ];
    }
}
