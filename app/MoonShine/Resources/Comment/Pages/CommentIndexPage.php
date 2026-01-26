<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Comment\Pages;

use App\MoonShine\Resources\Comment\CommentResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<CommentResource>
 */
class CommentIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Article'),
            BelongsTo::make('User'),
            Text::make('Text')->copy(),
        ];
    }
}
