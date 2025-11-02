<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Article;

use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\Comment\CommentResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends FormPage<ArticleResource>
 */
final class ArticleFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),

            Grid::make([
                Column::make([
                    Box::make('Main information', [
                        ActionButton::make(
                            'Link to article',
                            fn() => $this->getResource()?->getItem()?->getKey() ? route('articles.show', $this->getResource()?->getItem()) : '/',
                        )
                            ->icon('paper-clip')
                            ->blank(),

                        LineBreak::make(),

                        BelongsTo::make('Author', resource: MoonShineUserResource::class)
                            ->asyncSearch()
                            ->canSee(fn () => auth()->user()->isSuperUser())
                            ->required(),

                        Collapse::make('Title/Slug', [
                            Heading::make('Title/Slug'),

                            Flex::make([
                                Text::make('Title')
                                    ->withoutWrapper()
                                    ->required()
                                ,

                                Slug::make('Slug')
                                    ->from('title')
                                    ->unique()
                                    ->separator('-')
                                    ->withoutWrapper()
                                    ->required()
                                ,
                            ])
                                ->name('flex-titles')
                                ->justifyAlign('start')
                                ->itemsAlign('start'),
                        ]),

                        Fieldset::make('Files', [
                            Image::make('Thumbnail')
                                ->removable()
                                ->disk('public')
                                ->dir('articles'),

                            /*File::make('Files')
                                ->disk('public')
                                ->multiple()
                                ->removable()
                                ->dir('articles'),*/
                        ]),

                        Preview::make('No input field', 'no_input', static fn () => fake()->realText()),

                        RangeSlider::make('Age')
                            ->min(0)
                            ->max(60)
                            ->step(1)
                            ->fromTo('age_from', 'age_to'),

                        Number::make('Rating')
                            ->hint('From 0 to 5')
                            ->min(0)
                            ->max(5)
                            ->link('https://cutcode.dev', 'CutCode', blank: true)
                            ->stars(),

                        Url::make('Link')
                            ->hint('Url')
                            ->link('https://cutcode.dev', 'CutCode', blank: true)
                            ->suffix('url')
                        ,

                        Color::make('Color'),

                        //Code::make('Code'),

                        Json::make('Data')->fields([
                            Text::make('Title'),
                            Text::make('Value'),
                        ])->creatable()->removable(),

                        Switcher::make('Active'),
                    ]),
                ])->columnSpan(6),

                Column::make([
                    Box::make('Seo and categories', [
                        Tabs::make([
                            Tab::make('Seo', [
                                Text::make('Seo title')
                                    ->withoutWrapper(),

                                Text::make('Seo description')
                                    ->withoutWrapper(),

                                TinyMce::make('Description')
                                    ->addPlugins(['code', 'codesample'])
                                    ->toolbar(' | code codesample')
                                    ->required()
                                ,
                            ]),

                            Tab::make('Categories', [
                                BelongsToMany::make('Categories')
                                    ->horizontalMode()
                                //->tree('category_id')
                                ,
                            ]),
                        ]),
                    ]),
                ])->columnSpan(6),
            ]),

            HasMany::make('Comments', resource: CommentResource::class)
                ->async()
                ->creatable()
            ,

            HasOne::make('Comment', resource: CommentResource::class)
                ->async()
            ,
        ];
    }
}
