<?php

namespace App\MoonShine\Resources;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class CategoryResource extends ModelResource
{
    protected string $model = Category::class;

    protected string $title = 'Category';

    protected string $column = 'title';

    protected bool $withPolicy = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected array $with = ['category'];

    protected string $sortColumn = 'sorting';

    protected ?PageType $redirectAfterSave = PageType::DETAIL;

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
}
