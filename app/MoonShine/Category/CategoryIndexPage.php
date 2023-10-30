<?php

declare(strict_types=1);

namespace App\MoonShine\Category;

use Leeto\MoonShineTree\View\Components\TreeComponent;
use MoonShine\Pages\Crud\IndexPage;

class CategoryIndexPage extends IndexPage
{
    protected function mainLayer(): array
    {
        return [
            ...$this->actionButtons(),
            TreeComponent::make($this->getResource()),
        ];
    }
}
