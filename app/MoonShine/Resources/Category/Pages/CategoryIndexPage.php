<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use App\MoonShine\Resources\Category\CategoryResource;
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
            TreeComponent::make($this->getResource()),
        ];
    }
}
