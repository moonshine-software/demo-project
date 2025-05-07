<?php

namespace App\MoonShine\Resources;

use App\Models\Category;
use App\MoonShine\Pages\Category\CategoryIndexPage;
use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Group('Blog', 'newspaper')]
#[Icon('document')]
#[Order(3)]
class CategoryResource extends TreeResource
{
    protected string $model = Category::class;

    protected string $title = 'Categories';

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
            CategoryIndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Category')->nullable(),
            Text::make('Title')->required(),
        ];
    }

    protected function formFields(): iterable
	{
		return [
            Box::make($this->indexFields())
        ];
	}

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @param  Category  $item
     *
     */
    protected function rules(mixed $item): array
	{
	    return [
            'title' => ['required', 'string', 'min:5'],
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
        ];
    }

    public function treeKey(): ?string
    {
        return 'category_id';
    }

    public function sortKey(): string
    {
        return $this->getSortColumn();
    }
}
