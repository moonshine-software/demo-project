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
use Leeto\MoonShine\Decorations\Flex;
use Leeto\MoonShine\Decorations\Grid;
use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Decorations\Tabs;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\TinyMce;
use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Filters\TextFilter;
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

            Flex::make('flex-blocks', [
                Block::make('form-left', [
                    Button::make(
                        'Link to article',
                        $this->getItem() ? route('articles.show', $this->getItem()) : '/',
                        true
                    )->icon('clip'),

                    BelongsTo::make('Author', resource: 'name')
                        ->canSee(fn() => auth('moonshine')->user()->moonshine_user_role_id === 1)
                        ->required(),

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
                        ->withoutSpace()
                        ->justifyAlign('start')
                        ->itemsAlign('start'),

                    Image::make('Thumbnail')
                        ->disk('public')
                        ->dir('articles'),


                ]),

                Block::make('form-right', [
                    Tabs::make([
                        Tab::make('Seo', [
                            Text::make('Seo title')
                                ->hideOnIndex(),

                            Text::make('Seo description')
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
                                ->valuesQuery(fn(Builder $query) => $query)
                                ->hideOnIndex(),
                        ])
                    ])


                ]),
            ]),


            HasMany::make('Comments')
                ->hideOnIndex()
                ->resourceMode()
        ];
    }

    public function metrics(): array
    {
        return [
            ValueMetric::make('Articles')
                ->value(Article::query()->count()),

            ValueMetric::make('Comments')
                ->value(Comment::query()->count()),
        ];
    }

    public function query(): Builder
    {
        return parent::query()->when(
            auth('moonshine')->user()->moonshine_user_role_id !== 1,
            fn($q) => $q->where('author_id', auth('moonshine')->id())
        );
    }

    public function trStyles(Model $item, int $index): string
    {
        if ($item->author?->moonshine_user_role_id == 2) {
            return 'background-color: rgba(118 101 255 / 0.2);';
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

    public function search(): array
    {
        return ['id', 'title'];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Title'),
            BelongsToFilter::make('Author', resource: 'name')
                ->canSee(fn() => auth('moonshine')->user()->moonshine_user_role_id === 1),
            TextFilter::make('Slug'),
            BelongsToManyFilter::make('Categories')
                ->select()
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
