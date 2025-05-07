<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Category;

use App\MoonShine\Resources\CategoryResource;
use Leeto\MoonShineTree\View\Components\TreeComponent;
use MoonShine\Laravel\Pages\Crud\IndexPage;

/**
 * @extends IndexPage<CategoryResource>
 */
class CategoryIndexPage extends IndexPage
{
    protected function mainLayer(): array
    {
        return [
            ...$this->getPageButtons(),
            TreeComponent::make($this->getResource()),
        ];
    }
}
