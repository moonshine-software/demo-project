<?php

namespace App\MoonShine\Resources;

use App\Models\Setting;
use App\MoonShine\Pages\SettingPage;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class SettingResource extends ModelResource
{
    protected string $model = Setting::class;

    protected string $title = 'Setting';

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Email::make('Email'),
                Phone::make('Phone'),
                Text::make('Copyright')
            ])
        ];
    }

    protected function pages(): array
    {
        return [
            SettingPage::make()
        ];
    }

    public function getItemID(): int|string|null
    {
        return 1;
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function search(): array
    {
        return [];
    }
}
