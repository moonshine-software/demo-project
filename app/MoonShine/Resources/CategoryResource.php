<?php

namespace App\MoonShine\Resources;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Fields\ID;

class CategoryResource extends Resource
{
	public static string $model = Category::class;

	public static string $title = 'Category';

    public string $titleField = 'title';

    public static bool $withPolicy = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

	public function fields(): array
	{
		return [
            ID::make()->sortable(),
            Text::make('Title')->required(),
        ];
	}

	public function rules(Model $item): array
	{
	    return [
            'title' => ['required', 'string', 'min:1'],
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
