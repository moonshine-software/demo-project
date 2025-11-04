<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Comment\Pages;

use App\MoonShine\Resources\Comment\CommentResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<CommentResource>
 */
class CommentDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('Article'),
            BelongsTo::make('User'),
            Text::make('Text'),
        ];
    }
}
