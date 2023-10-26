<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Controllers\ArticleController;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\LineBreak;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Decorations\TextBlock;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Color;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\File;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Preview;
use MoonShine\Fields\RangeSlider;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Slug;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Fields\Url;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Metrics\ValueMetric;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;
use MoonShine\Resources\MoonShineUserResource;

class ArticleResource extends ModelResource
{
    public string $model = Article::class;

    public string $title = 'Articles';

    public string $sortColumn = 'created_at';

    public bool $withPolicy = true;

    public array $with = [
        'author',
        'comments',
    ];

    public string $column = 'title';

    protected ?PageType $redirectAfterSave = PageType::INDEX;

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
                        ActionButton::make(
                            'Link to article',
                            $this->getItem()?->getKey() ? route('articles.show', $this->getItem()) : '/',
                        )
                            ->icon('heroicons.outline.paper-clip')
                            ->blank(),

                        LineBreak::make(),

                        BelongsTo::make('Author', resource: new MoonShineUserResource())
                            ->asyncSearch()
                            ->canSee(fn () => auth()->user()->moonshine_user_role_id === 1)
                            ->required(),

                        Number::make('Comments', 'comments_count')
                            ->hideOnForm(),

                        Collapse::make('Title/Slug', [
                            Heading::make('Title/Slug'),

                            Flex::make('flex-titles', [
                                Text::make('Title')
                                    ->withoutWrapper()
                                    ->required(),

                                Slug::make('Slug')
                                    ->from('title')
                                    ->unique()
                                    ->separator('-')
                                    ->hideOnIndex()
                                    ->withoutWrapper()
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

                        Preview::make('No input field', 'no_input', static fn () => fake()->realText())
                            ->hideOnIndex(),


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
                            ->expansion('url'),

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
                    Block::make('Seo and categories', [
                        Tabs::make([
                            Tab::make('Seo', [
                                Text::make('Seo title')
                                    ->withoutWrapper()
                                    ->hideOnIndex(),

                                Text::make('Seo description')
                                    ->withoutWrapper()
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
                            ]),
                        ]),
                    ]),
                ])->columnSpan(6),
            ]),

            HasMany::make('Comments', resource: new CommentResource())
                ->async()
                ->creatable()
                ->hideOnDetail()
                ->hideOnIndex(),


            HasOne::make('Comment', resource: new CommentResource())
                ->hideOnDetail()
                ->hideOnIndex(),
        ];
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
            )->icon('heroicons.outline.users'),
        ];
    }

    public function metrics(): array
    {
        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Articles')
                        ->value(Article::query()->count()),
                ])->columnSpan(6),
                Column::make([
                    ValueMetric::make('Comments')
                        ->value(Comment::query()->count()),
                ])->columnSpan(6),
            ]),
        ];
    }

    public function query(): Builder
    {
        return parent::query()
            ->withCount('comments')
            ->when(
                auth()->user()->moonshine_user_role_id !== 1,
                fn ($q) => $q->where('author_id', auth()->id())
            );
    }

    public function trAttributes(): Closure
    {
        return function (mixed $data, int $row, ComponentAttributeBag $attr): ComponentAttributeBag {
            return $attr->when(
                $data->author?->moonshine_user_role_id !== 1,
                fn (ComponentAttributeBag $a) => $a->merge(['class' => 'bgc-green'])
            );
        };
    }

    public function rules(Model $item): array
    {
        return [
            'title' => ['required', 'string', 'min:2'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
            'thumbnail' => ['image'],
        ];
    }

    protected function beforeCreating(Model $item): Model
    {
        if (auth()->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth()->id(),
            ]);
        }

        return $item;
    }

    protected function beforeUpdating(Model $item): Model
    {
        if (auth()->user()->moonshine_user_role_id !== 1) {
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

            BelongsTo::make('Author', resource: new UserResource())
                ->nullable()
                ->canSee(fn () => auth()->user()->moonshine_user_role_id === 1),

            Slug::make('Slug'),

            BelongsToMany::make('Categories')
                ->selectMode(),

            Switcher::make('Active'),
        ];
    }

    public function buttons(): array
    {
        return [
            ActionButton::make('Active', route('moonshine.articles.mass-active', $this->uriKey()))
                ->inModal(fn () => 'Active', fn (ActionButton $action): string => (string) form(
                    $action->url(),
                    fields: [
                        Hidden::make('ids')->customAttributes([
                            'class' => 'actionsCheckedIds',
                        ]),
                        TextBlock::make('', __('moonshine::ui.confirm_message')),
                        Text::make('To confirm, write "yes"', 'confirm')
                            ->customAttributes(['placeholder' => 'Or no']),
                    ]
                )
                    ->async()
                    ->submit(__('moonshine::ui.delete'), ['class' => 'btn-secondary']))
                ->bulk(),

            ActionButton::make(
                'Go to',
                static fn (Article $model) => route('articles.show', $model)
            )->icon('heroicons.outline.paper-clip'),
        ];
    }

    public function formButtons(): array
    {
        return [
            DeleteButton::for($this),
        ];
    }

    public function export(): ?ExportHandler
    {
        return null;
    }

    public function import(): ?ImportHandler
    {
        return null;
    }

    protected function resolveRoutes(): void
    {
        parent::resolveRoutes();

        Route::post('mass-active', [ArticleController::class, 'massActive'])
            ->name('articles.mass-active');
    }
}
