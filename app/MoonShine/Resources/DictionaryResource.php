<?php

namespace App\MoonShine\Resources;

use App\Models\Dictionary;
use App\MoonShine\Fields\CKEditor;
use App\MoonShine\Fields\Quill;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\TinyMce;
use Leeto\MoonShine\Fields\WYSIWYG;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Fields\ID;

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
