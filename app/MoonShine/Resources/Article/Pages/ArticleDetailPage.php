<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\MoonShine\Resources\Article\ArticleResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends DetailPage<ArticleResource>
 */
final class ArticleDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),

            BelongsTo::make('Author', resource: MoonShineUserResource::class),

            Number::make('Comments', 'comments_count'),

            Text::make('Title'),

            RangeSlider::make('Age')->fromTo('age_from', 'age_to'),

            Number::make('Rating')
                ->link('https://cutcode.dev', 'CutCode', blank: true)
                ->stars(),

            Url::make('Link')
                ->link('https://cutcode.dev', 'CutCode', blank: true)
                ->customWrapperAttributes(['style' => 'white-space: normal;'])
            ,

            Color::make('Color'),

            Switcher::make('Active'),
        ];
    }

    /** @param  TableBuilder  $component */
    public function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component->vertical(
            title: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(2),
            value: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(10),
        );
    }
}
