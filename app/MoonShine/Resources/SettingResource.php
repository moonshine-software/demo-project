<?php

namespace App\MoonShine\Resources;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\FiltersAction;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\Resources\SingletonResource;

class SettingResource extends SingletonResource
{
    public static string $model = Setting::class;

    public static string $title = 'Setting';

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

    public function rules(Model $item): array
    {
        return [];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [
            FiltersAction::make(trans('moonshine::ui.filters')),
        ];
    }

    public function getId(): int|string
    {
        return 1;
    }
}
