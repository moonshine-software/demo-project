<?php

namespace App\MoonShine\Resources;

use App\Models\Category;
use App\MoonShine\Category\CategoryIndexPage;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class CategoryResource extends TreeResource
{
    protected string $model = Category::class;

    protected string $title = 'Category';

    protected string $column = 'title';

    protected bool $withPolicy = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected array $with = ['category'];

    protected string $sortColumn = 'sorting';

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    protected function pages(): array
    {
        return [
            CategoryIndexPage::make($this->title()),
            FormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            DetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function fields(): array
	{
		return [
            Block::make([
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

    public function treeKey(): ?string
    {
        return 'category_id';
    }

    public function sortKey(): string
    {
        return $this->sortColumn();
    }
}
