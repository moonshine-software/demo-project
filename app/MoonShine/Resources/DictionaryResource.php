<?php

namespace App\MoonShine\Resources;

use App\Models\Dictionary;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Decorations\Block;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class DictionaryResource extends ModelResource
{
    protected string $model = Dictionary::class;

    protected string $title = 'Dictionary';

    protected bool $withPolicy = true;

    public function fields(): array
    {
        return [
            Block::make([
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
            Text::make('Title')
        ];
    }
}
