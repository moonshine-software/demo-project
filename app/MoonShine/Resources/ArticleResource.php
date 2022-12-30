<?php

namespace App\MoonShine\Resources;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\TinyMce;
use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Resources\Resource;

class ArticleResource extends Resource
{
	public static string $model = Article::class;

	public static string $title = 'Articles';

    public static string $orderField = 'created_at';

    public static string $orderType = 'DESC';

    public static bool $withPolicy = true;

    public static array $with = [
        'author'
    ];

	public function fields(): array
	{
		return [
            ID::make()->sortable(),

            BelongsTo::make('Author', resource: 'name')
                ->canSee(fn() => auth('moonshine')->user()->moonshine_user_role_id === 1)
                ->required(),

            Text::make('Title')->required(),

            Text::make('Slug')
                ->hideOnIndex()
                ->required(),

            TinyMce::make('Description')
                ->commentAuthor('Danil Shutsky')
                ->addPlugins('code codesample')
                ->addToolbar(' | code codesample')
                ->required()
                ->hideOnIndex(),

            Image::make('Thumbnail')
                ->dir('articles'),

            BelongsToMany::make('Categories')
                ->valuesQuery(fn(Builder $query) => $query)
                ->hideOnIndex(),

            Heading::make('Seo'),

            Text::make('Seo title')
                ->hideOnIndex(),

            Text::make('Seo description')
                ->hideOnIndex(),
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
        if($item->author?->moonshine_user_role_id == 2) {
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
        if(auth('moonshine')->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth('moonshine')->id()
            ]);
        }
    }

    protected function beforeUpdating(Model $item)
    {
        if(auth('moonshine')->user()->moonshine_user_role_id !== 1) {
            request()->merge([
                'author_id' => auth('moonshine')->id()
            ]);
        }
    }

    public function itemActions(): array
    {
        return [
            ItemAction::make('Go to', function (Article $model) {
                header("Location: " . route('articles.show', $model));

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
        ];
    }

    public function actions(): array
    {
        return [];
    }
}
