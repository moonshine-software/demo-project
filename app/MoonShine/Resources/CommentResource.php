<?php

namespace App\MoonShine\Resources;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Fields\ID;

class CommentResource extends Resource
{
	public static string $model = Comment::class;

	public static string $title = 'Comments';

    public static array $with = ['user', 'article'];
	public function fields(): array
	{
		return [
            Block::make('', [
                ID::make()->sortable(),
                BelongsTo::make('Article'),
                BelongsTo::make('User'),
                Text::make('Text')->required(),
            ])
        ];
	}

	public function rules(Model $item): array
	{
	    return [
            'text' => ['required', 'string', 'min:1'],
        ];
    }

    public function search(): array
    {
        return ['id', 'text'];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [];
    }
}
