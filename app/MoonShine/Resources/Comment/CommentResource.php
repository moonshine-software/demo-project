<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Comment;

use App\Models\Comment;
use App\MoonShine\Resources\Comment\Pages\CommentIndexPage;
use App\MoonShine\Resources\Comment\Pages\CommentFormPage;
use App\MoonShine\Resources\Comment\Pages\CommentDetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Comment, CommentIndexPage, CommentFormPage, CommentDetailPage>
 */
#[Group('Blog', 'newspaper')]
#[Icon('chat-bubble-left')]
#[Order(3)]
class CommentResource extends ModelResource
{
    protected string $model = Comment::class;

    protected string $title = 'Comments';

    protected array $with = ['user', 'article'];

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            CommentIndexPage::class,
            CommentFormPage::class,
            CommentDetailPage::class,
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
