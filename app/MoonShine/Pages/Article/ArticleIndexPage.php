<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Article;

use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\MoonShineUserResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends IndexPage<ArticleResource>
 */
final class ArticleIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return array_filter([
            ID::make()->sortable(),

            BelongsTo::make('Author', resource: MoonShineUserResource::class),

            Number::make('Comments', 'comments_count'),

            Text::make('Title'),

            $this->getResource()?->isListView() ?
                Fieldset::make('Files', [
                    Image::make('Thumbnail')
                        ->disk('public')
                        ->dir('articles'),
                ]) : null,

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
        ]);
    }
}
