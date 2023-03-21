<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Actions\ImportAction;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Dashboard\ResourcePreview;
use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Decorations\Button;
use Leeto\MoonShine\Decorations\Collapse;
use Leeto\MoonShine\Decorations\Column;
use Leeto\MoonShine\Decorations\Flex;
use Leeto\MoonShine\Decorations\Grid;
use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Decorations\Tabs;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Fields\CKEditor;
use Leeto\MoonShine\Fields\Code;
use Leeto\MoonShine\Fields\Color;
use Leeto\MoonShine\Fields\File;
use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\HasOne;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\NoInput;
use Leeto\MoonShine\Fields\Number;
use Leeto\MoonShine\Fields\SlideField;
use Leeto\MoonShine\Fields\SwitchBoolean;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\TinyMce;
use Leeto\MoonShine\Fields\Url;
use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Filters\DateRangeFilter;
use Leeto\MoonShine\Filters\SlideFilter;
use Leeto\MoonShine\Filters\SwitchBooleanFilter;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\FormActions\FormAction;
use Leeto\MoonShine\QueryTags\QueryTag;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Metrics\ValueMetric;
use Leeto\MoonShine\Resources\Resource;

class ArticleResource extends Resource
{
    public static string $model = Article::class;

    public static string $title = 'Articles';

    public static string $orderField = 'created_at';

    public static string $orderType = 'DESC';

    public static bool $withPolicy = true;

    public static array $with = [
        'author',
        'comments'
    ];

    public string $titleField = 'title';

    public function fields(): array
    {
        return [
            ID::make()
                ->useOnImport()
                ->showOnExport()
                ->sortable(),

            Grid::make([
                Column::make([
                    Block::make('Main information', [
                        Button::make(
                            'Link to article',
                            $this->getItem() ? route('articles.show', $this->getItem()) : '/',
                            true
                        )->icon('clip'),

                        BelongsTo::make('Author', resource: 'name')
                            ->canSee(fn() => auth('moonshine')->user()->moonshine_user_role_id === 1)
                            ->required(),

                        Number::make('Comments', 'comments_count')
                            ->hideOnForm(),

                        Collapse::make('Title/Slug', [
                            Heading::make('Title/Slug'),

                            Flex::make('flex-titles', [
                                Text::make('Title')
                                    ->fieldContainer(false)
                                    ->required(),

                                Text::make('Slug')
                                    ->hideOnIndex()
                                    ->fieldContainer(false)
                                    ->required(),
                            ])
                                ->justifyAlign('start')
                                ->itemsAlign('start'),
                        ]),

                        Image::make('Thumbnail')
                            ->removable()
                            ->disk('public')
                            ->dir('articles'),

                        File::make('Files')
                            ->disk('public')
                            ->multiple()
                            ->removable()
                            ->dir('articles'),

                        NoInput::make('No input field', 'no_input', static fn() => fake()->realText())
                            ->hideOnIndex(),


                        SlideField::make('Age')
                            ->min(0)
                            ->max(60)
                            ->step(1)
                            ->toField('age_to')
                            ->fromField('age_from'),

                        Number::make('Rating')
                            ->hint('From 0 to 5')
                            ->min(0)
                            ->max(5)
                            ->stars(),

                        Url::make('Link')
                            ->addLink('CutCode', 'https://cutcode.dev', true)
                            ->expansion('url'),

                        Color::make('Color'),

                        //Code::make('Code'),

                        Json::make('Data')->fields([
                            Text::make('Title'),
                            Text::make('Value')
                        ])->removable(),

                        SwitchBoolean::make('Active')
                    ]),
                ])->columnSpan(6),

                Column::make([
                    Block::make('Comments', [
                        HasMany::make('Comments')
                            ->fields([
                                ID::make()->sortable(),
                                BelongsTo::make('Article'),
                                BelongsTo::make('User'),
                                Text::make('Text')->required(),
                                Image::make('Files')
                                    ->multiple()
                                    ->removable()
                                    ->disk('public')
                                    ->dir('comments'),
                            ])
                            ->removable()
                            ->hideOnIndex()
                            ->fullPage(),
                    ]),

                   /* Block::make([
                        HasOne::make('Comment')
                            ->fields([
                                ID::make()->sortable(),
                                BelongsTo::make('Article'),
                                BelongsTo::make('User'),
                                Text::make('Text')->required(),
                            ])
                            ->removable()
                            ->hideOnIndex()
                            ->fullPage()
                    ]),*/

                    Block::make('Seo and categories', [
                        Tabs::make([
                            Tab::make('Seo', [
                                Text::make('Seo title')
                                    ->fieldContainer(false)
                                    ->hideOnIndex(),

                                Text::make('Seo description')
                                    ->fieldContainer(false)
                                    ->hideOnIndex(),

                                TinyMce::make('Description')
                                    ->commentAuthor('Danil Shutsky')
                                    ->addPlugins('code codesample')
                                    ->addToolbar(' | code codesample')
                                    ->required()
                                    ->fullWidth()
                                    ->hideOnIndex(),
                            ]),

                            Tab::make('Categories', [
                                BelongsToMany::make('Categories')
                                    ->tree('category_id')
                                    ->fields([
                                        Text::make('Text'),
                                        Text::make('Value')
                                    ])
                                    ->valuesQuery(fn(Builder $query) => $query)
                                    ->hideOnIndex(),
                            ])
                        ])
                    ]),
                ])->columnSpan(6),
            ]),

            HasMany::make('Comments')
                ->hideOnIndex()
                ->resourceMode()
        ];
    }

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Article with author',
                Article::query()->whereNotNull('author_id')
            ),

            QueryTag::make(
                'Article without an author',
                Article::query()->whereNull('author_id')
            )->icon('users')
        ];
    }

    public function metrics(): array
    {
        return [
            ValueMetric::make('Articles')
                ->value(Article::query()->count())
                ->columnSpan(6),

            ValueMetric::make('Comments')
                ->value(Comment::query()->count())
                ->columnSpan(6),
        ];
    }

    public function query(): Builder
    {
        return parent::query()
            ->withCount('comments')
            ->when(
                auth('moonshine')->user()->moonshine_user_role_id !== 1,
                fn($q) => $q->where('author_id', auth('moonshine')->id())
            );
    }

    public function trStyles(Model $item, int $index): string
    {
        if ($item->author?->moonshine_user_role_id === 2) {
            return 'green';
        }

        return parent::trStyles($item, $index);
    }

    public function rules(Model $item): array
    {
        return [
            'title' => ['required', 'string', 'min:1'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
            'thumbnail' => ['image']
        ];
    }

    protected function beforeCreating(Model $item)
    {
        if (auth('moonshine')->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth('moonshine')->id()
            ]);
        }
    }

    protected function beforeUpdating(Model $item)
    {
        if (auth('moonshine')->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth('moonshine')->id()
            ]);
        }
    }

    public function itemActions(): array
    {
        return [
            ItemAction::make('Go to', function (Article $model) {
                header("Location: ".route('articles.show', $model));

                die();
            })->icon('clip')
        ];
    }

    public function formActions(): array
    {
        return [
            FormAction::make('Delete', function (Article $model) {
                $model->delete();
            })->icon('delete'),

            FormAction::make('Preview', function (Article $model) {
                header("Location: ".route('articles.show', $model));

                die();
            })->icon('clip')
        ];
    }

    public function search(): array
    {
        return ['id', 'title'];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Title'),

            BelongsToFilter::make('Author', resource: 'name')
                ->nullable()
                ->canSee(fn() => auth('moonshine')->user()->moonshine_user_role_id === 1),

            TextFilter::make('Slug'),

            BelongsToManyFilter::make('Categories')
                ->select(),

            DateRangeFilter::make('Created at'),

            SlideFilter::make('Age')
                ->fromField('age_from')
                ->toField('age_to')
                ->min(0)
                ->max(60),

            SwitchBooleanFilter::make('Active')
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make('Export')
                ->disk('public')
                ->dir('exports')
                ->queue(),

            ImportAction::make('Import')
                ->disk('public')
                ->dir('imports')
                ->deleteAfter()
                ->queue()
        ];
    }
}
