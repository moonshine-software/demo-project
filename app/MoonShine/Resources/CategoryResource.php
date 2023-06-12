<?php

namespace App\MoonShine\Resources;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Filters\TextFilter;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;

class CategoryResource extends TreeResource
{
	public static string $model = Category::class;

	public static string $title = 'Category';

    public string $titleField = 'title';

    public static bool $withPolicy = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    public static array $with = ['category'];

    public static string $orderField = 'sorting';

    public function treeKey(): ?string
    {
        return 'category_id';
    }

    public function sortKey(): string
    {
        return 'sorting';
    }

	public function fields(): array
	{
		return [
            Block::make('', [
                ID::make()->sortable(),
                BelongsTo::make('Category')
                    ->nullable(),
                Text::make('Title')->required(),
            ])
        ];
	}

	public function rules(Model $item): array
	{
	    return [
            'title' => ['required', 'string', 'min:5'],
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
