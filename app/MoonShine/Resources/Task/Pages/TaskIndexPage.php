<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task\Pages;

use Leeto\MoonShineKanBan\View\Components\KanBanComponent;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\ListOf;

final class TaskIndexPage extends IndexPage
{
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return KanBanComponent::make($this->getResource(), $this->getResource()->getItems());
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return parent::modifyEditButton($button)
            ->setLabel(__('moonshine::ui.edit'))
            ->class('btn-sm')
            ->square(false);
    }
}
