<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Task\Pages;

use Leeto\MoonShineKanBan\View\Components\KanBanComponent;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;

final class TaskIndexPage extends IndexPage
{
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return KanBanComponent::make($this->getResource(), $this->getResource()->getItems());
    }
}
