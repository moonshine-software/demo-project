<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use App\Models\Comment;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\StackFields;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

class ArticleResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    public string $model = Article::class;

    public string $title = 'Articles';

    public string $sortColumn = 'created_at';

    public bool $withPolicy = true;

    protected bool $columnSelection = true;

    protected bool $stickyButtons = true;

    public array $with = [
        'author',
        'comments',
    ];

    public string $column = 'title';

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Slug::make('Slug'),
        ];
    }

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Slug::make('Slug'),
        ];
    }

    public function indexFields(): iterable
    {
        return [
            ID::make()
                ->sortable(),

            BelongsTo::make('Author', resource: MoonShineUserResource::class)
                ->asyncSearch()
                ->canSee(fn () => auth()->user()->isSuperUser())
                ->required(),

            Number::make('Comments', 'comments_count'),

            Text::make('Title')
                ->withoutWrapper()
                ->required()
            ,

            StackFields::make('Files')->fields([
                Image::make('Thumbnail')
                    ->removable()
                    ->disk('public')
                    ->dir('articles'),

                /* Or
                 * File::make('Files')
                    ->disk('public')
                    ->multiple()
                    ->removable()
                    ->dir('articles'),
                */
            ]),

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
                ->customWrapperAttributes(['style' => 'white-space: normal;'])
            ,

            Color::make('Color')->default('red'),

            Json::make('Data')->fields([
                Text::make('Title'),
                Text::make('Value'),
            ])->creatable()->removable(),

            Switcher::make('Active'),
        ];
    }

    public function formFields(): iterable
    {
        return [
            ID::make(),

            Grid::make([
                Column::make([
                    Box::make('Main information', [
                        ActionButton::make(
                            'Link to article',
                            $this->getItem()?->getKey() ? route('articles.show', $this->getItem()) : '/',
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

                        StackFields::make('Files')->fields([
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
                                BelongsToMany::make('Categories')->tree('category_id'),
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

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /** @param TableBuilder $component */
    public function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component->vertical(
            title: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(2),
            value: fn(FieldContract $field, Column $default, TableBuilder $ctx) => $default->columnSpan(10),
        );
    }

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Article with author',
                static fn (Builder $q) => $q->whereNotNull('author_id')
            ),

            QueryTag::make(
                'Article without an author',
                static fn (Builder $q) => $q->whereNull('author_id')
            )->icon('users'),
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

    /**
     * @throws \Throwable
     */
    public function query(): Builder
    {
        return parent::getQuery()
            ->withCount('comments')
            ->when(
                !auth()->user()->isSuperUser(),
                fn ($q) => $q->where('author_id', auth()->id())
            );
    }

    public function trAttributes(): Closure
    {
        return function (?DataWrapperContract $data, int $row): array {
            if($data?->getOriginal()->author?->moonshine_user_role_id !== 1) {
                return [
                    'class' => 'bgc-gray'
                ];
            }

            return [];
        };
    }

    public function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:2'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
            'thumbnail' => ['image'],
        ];
    }

    protected function beforeCreating(mixed $item): Model
    {
        if (!auth()->user()->isSuperUser()) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    protected function beforeUpdating(mixed $item): Model
    {
        if (!auth()->user()->isSuperUser()) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    public function search(): array
    {
        return ['id', 'title'];
    }

    public function filters(): array
    {
        return [
            Text::make('Title'),

            BelongsTo::make('Author', resource: UserResource::class)
                ->nullable()
                ->canSee(fn () => auth()->user()->isSuperUser()),

            Slug::make('Slug'),

            BelongsToMany::make('Categories')
                ->selectMode(),

            Switcher::make('Active'),
        ];
    }

    public function indexButtons(): ListOf
    {
        $tableName = $this->getIndexPage()->getListComponentName();

        return new ListOf(ActionButtonContract::class, [
            ...parent::indexButtons()->toArray(),

            ActionButton::make('Active', route('moonshine.articles.mass-active', $this->getUriKey()))
                ->inModal(fn () => 'Active', fn (): string => (string) FormBuilder::make(
                    route('moonshine.articles.mass-active', $this->getUriKey()),
                    fields: [
                        HiddenIds::make($tableName),
                        FlexibleRender::make('<div>' . __('moonshine::ui.confirm_message') . '</div>'),
                        Text::make('To confirm, write "yes"', 'confirm')
                            ->customAttributes(['placeholder' => 'Or no']),
                    ]
                )
                    ->async(events: [AlpineJs::event(JsEvent::TABLE_UPDATED, $tableName)])
                    ->submit(__('moonshine::ui.confirm'), ['class' => 'btn-secondary']))
                    ->bulk()
            ,

            ActionButton::make(
                'Go to',
                static fn (Article $model) => route('articles.show', $model)
            )->icon('paper-clip'),
        ]);
    }
}
