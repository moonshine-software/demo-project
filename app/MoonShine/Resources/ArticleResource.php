<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\DetailComponents\ExampleDetailComponent;
use App\MoonShine\FormComponents\ExampleFormComponent;
use App\MoonShine\IndexComponents\ExampleIndexComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\ExportAction;
use MoonShine\Actions\ImportAction;
use MoonShine\BulkActions\BulkAction;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Button;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Color;
use MoonShine\Fields\File;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\HasOne;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Json;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Number;
use MoonShine\Fields\SlideField;
use MoonShine\Fields\Slug;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Fields\Url;
use MoonShine\Filters\BelongsToFilter;
use MoonShine\Filters\BelongsToManyFilter;
use MoonShine\Filters\DateRangeFilter;
use MoonShine\Filters\SlideFilter;
use MoonShine\Filters\SwitchBooleanFilter;
use MoonShine\Filters\TextFilter;
use MoonShine\FormActions\FormAction;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\ItemActions\ItemAction;
use MoonShine\Metrics\ValueMetric;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\Resource;

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
                            ->asyncSearch()
                            ->canSee(fn() => auth()->user()->moonshine_user_role_id === 1)
                            ->required(),

                        Number::make('Comments', 'comments_count')
                            ->hideOnForm(),

                        Collapse::make('Title/Slug', [
                            Heading::make('Title/Slug'),

                            Flex::make('flex-titles', [
                                Text::make('Title')
                                    ->fieldContainer(false)
                                    ->required(),

                                Slug::make('Slug')
                                    ->from('title')
                                    ->unique()
                                    ->separator('-')
                                    ->hideOnIndex()
                                    ->fieldContainer(false)
                                    ->required(),
                            ])
                                ->justifyAlign('start')
                                ->itemsAlign('start'),
                        ]),

                        StackFields::make('Files')->fields([
                            Image::make('Thumbnail')
                                ->removable()
                                ->disk('public')
                                ->dir('articles'),

                            File::make('Files')
                                ->disk('public')
                                ->multiple()
                                ->removable()
                                ->dir('articles'),
                        ]),

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
                            ->addLink('CutCode', 'https://cutcode.dev', true)
                            ->stars(),

                        Url::make('Link')
                            ->hint('Url')
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
                                    ->hideOnIndex(),
                            ]),

                            Tab::make('Categories', [
                                BelongsToMany::make('Categories')
                                    ->tree('category_id')
                                    ->hideOnIndex(),
                            ])
                        ])
                    ]),
                ])->columnSpan(6),
            ]),

            HasMany::make('Comments')
                ->hideOnIndex()
                ->resourceMode(),


            HasOne::make('Comment')
                ->hideOnIndex()
                ->resourceMode()
        ];
    }

    public function components(): array
    {
        return [
            ExampleIndexComponent::make('Example IndexComponent'),
            ExampleFormComponent::make('Example FormComponent'),
            ExampleDetailComponent::make('Example DetailComponent'),
            ChangeLogFormComponent::make('ChangeLog'),
        ];
    }

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Article with author',
                static fn(Builder $q) => $q->whereNotNull('author_id')
            ),

            QueryTag::make(
                'Article without an author',
                static fn(Builder $q) => $q->whereNull('author_id')
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
                auth()->user()->moonshine_user_role_id !== 1,
                fn($q) => $q->where('author_id', auth()->id())
            );
    }

    public function trClass(Model $item, int $index): string
    {
        if ($this->getItem()->author?->moonshine_user_role_id === 2) {
            return 'green';
        }

        return parent::trClass($item, $index);
    }

    public function rules(Model $item): array
    {
        return [
            'title' => ['required', 'string', 'min:2'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
            'thumbnail' => ['image']
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::make('Active', function (Article $article) {
                $article->update([
                    'active' => true
                ]);
            })->icon('heroicons.check-circle')
        ];
    }

    protected function beforeCreating(Model $item)
    {
        if (auth()->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth()->id()
            ]);
        }
    }

    protected function beforeUpdating(Model $item)
    {
        if (auth()->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth()->id()
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
                ->canSee(fn() => auth()->user()->moonshine_user_role_id === 1),

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
