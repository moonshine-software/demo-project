<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use App\MoonShine\Resources\Category\CategoryResource;
use Leeto\MoonShineTree\View\Components\TreeComponent;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;

/**
 * @extends IndexPage<CategoryResource>
 */
class CategoryIndexPage extends IndexPage
{
    protected function mainLayer(): array
    {
        return [
            Fragment::make([
                TreeComponent::make($this->getResource()),
            ])->name('crud-list'),
        ];
    }

    public function getListEventName(?string $name = null, array $params = []): string
    {
        return AlpineJs::event(JsEvent::FRAGMENT_UPDATED, 'crud-list', $params);
    }
}
