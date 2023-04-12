<?php

namespace App\MoonShine\Resources;

use App\Models\Dictionary;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Decorations\Block;
use MoonShine\Fields\Image;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Filters\TextFilter;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;

class DictionaryResource extends Resource
{
	public static string $model = Dictionary::class;

	public static string $title = 'Dictionary';

    public static bool $withPolicy = true;

	public function fields(): array
	{
		return [
            Block::make('', [
                ID::make()->sortable(),
                Text::make('Title')->required(),
                Text::make('Slug')->required(),

                TinyMce::make('Description')
                    //->required()
                    ->hideOnIndex(),
            ])
        ];
	}

	public function rules(Model $item): array
	{
	    return [
            'title' => ['required', 'string', 'min:1'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
        ];
    }

    public function search(): array
    {
        return ['id', 'title'];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Title')
        ];
    }

    public function actions(): array
    {
        return [];
    }
}
