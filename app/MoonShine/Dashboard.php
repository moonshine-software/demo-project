<?php

namespace App\MoonShine;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CommentResource;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Dashboard\DashboardScreen;
use Leeto\MoonShine\Dashboard\ResourcePreview;
use Leeto\MoonShine\Dashboard\ResourceTable;
use Leeto\MoonShine\Metrics\ValueMetric;

class Dashboard extends DashboardScreen
{
	public function blocks(): array
	{
		return [
            DashboardBlock::make([
                ValueMetric::make('Articles')
                    ->columnSpan(6)
                    ->value(Article::query()->count()),

                ValueMetric::make('Comments')
                    ->columnSpan(6)
                    ->value(Comment::query()->count()),
            ]),

            DashboardBlock::make([
                ValueMetric::make('Articles')
                    ->columnSpan(6)
                    ->value(Article::query()->count()),

                ValueMetric::make('Comments')
                    ->columnSpan(6)
                    ->value(Comment::query()->count()),
            ]),

            DashboardBlock::make([
                ResourcePreview::make(
                    new ArticleResource(),
                    'Latest articles',
                    Article::query()
                        ->with(['author'])
                        ->latest()
                        ->limit(2)
                )->columnSpan(6),

                ResourcePreview::make(
                    new CommentResource(),
                    'Latest comments'
                )->columnSpan(6),
            ])
        ];
	}
}
