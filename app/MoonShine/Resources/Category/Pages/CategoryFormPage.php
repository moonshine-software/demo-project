<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<CategoryResource>
 */
class CategoryFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                BelongsTo::make('Category', resource: CategoryResource::class)
                    ->nullable(),
                Text::make('Title')
                    ->required(),
            ])
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title' => ['required', 'string', 'min:5'],
        ];
    }
}
