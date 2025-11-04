<?php

namespace App\MoonShine\Resources\Setting;

use App\Models\Setting;
use App\MoonShine\Resources\Setting\Pages\SettingPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Setting, SettingPage>
 */
#[Icon('adjustments-vertical')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(0)]
class SettingResource extends ModelResource
{
    protected string $model = Setting::class;

    protected string $title = 'Settings';

    protected function onLoad(): void
    {
        parent::onLoad();

        $this->getActivePage()?->breadcrumbs([
            '#' => $this->getTitle(),
        ]);
    }

    protected function pages(): array
    {
        return [
            SettingPage::class,
        ];
    }

    public function getItemID(): int|string|null
    {
        return 1;
    }

    protected function search(): array
    {
        return [];
    }
}
