<?php

namespace App\MoonShine\Resources\Category;

use App\Models\Category;
use App\MoonShine\Resources\Category\Pages\CategoryDetailPage;
use App\MoonShine\Resources\Category\Pages\CategoryFormPage;
use App\MoonShine\Resources\Category\Pages\CategoryIndexPage;
use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\PageType;

/**
 * @extends ModelResource<Category, CategoryIndexPage, CategoryFormPage, CategoryDetailPage>
 */
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

    protected bool $detailInModal = true;

    protected array $with = ['category'];

    protected string $sortColumn = 'sorting';

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    protected function pages(): array
    {
        return [
            CategoryIndexPage::class,
            CategoryFormPage::class,
            CategoryDetailPage::class,
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
