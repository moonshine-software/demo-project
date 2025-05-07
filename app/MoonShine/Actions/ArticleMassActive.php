<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;


use App\Models\Article;

final readonly class ArticleMassActive
{
    /**
     * @param  int[]  $ids
     */
    public function __invoke(array $ids): bool
    {
        return Article::query()
            ->whereIn('id', $ids)
            ->update(['active' => true]) === 1;
    }
}
