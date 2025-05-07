<?php

namespace App\MoonShine\Resources;

use App\Models\Comment;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Group('Blog', 'newspaper')]
#[Icon('chat-bubble-left')]
#[Order(3)]
class CommentResource extends ModelResource
{
    protected string $model = Comment::class;

    protected string $title = 'Comments';

    protected array $with = ['user', 'article'];

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Article'),
            BelongsTo::make('User'),
            Text::make('Text')->required(),
        ];
    }

    protected function formFields(): array
	{
		return [
            Box::make([
                ...$this->indexFields()
            ])
        ];
	}

    protected function detailFields(): iterable
    {
        return [
            ...$this->indexFields()
        ];
    }

    /**
     * @param  Comment  $item
     */
    protected function rules(mixed $item): array
	{
	    return [
            'text' => ['required', 'string', 'min:1'],
        ];
    }

    protected function search(): array
    {
        return ['id', 'text'];
    }

    public function getBadge(): string
    {
        return (string) Comment::query()->count();
    }
}
