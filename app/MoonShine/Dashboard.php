<?php

namespace App\MoonShine;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CommentResource;
use MoonShine\Dashboard\DashboardBlock;
use MoonShine\Dashboard\DashboardScreen;
use MoonShine\Dashboard\ResourcePreview;
use MoonShine\Dashboard\ResourceTable;
use MoonShine\Dashboard\TextBlock;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;

class Dashboard extends DashboardScreen
{
	public function blocks(): array
	{
		return [
            DashboardBlock::make([
                TextBlock::make(
                    'Welcome to MoonShine!',
                    'Demo version'
                )
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
                DonutChartMetric::make('Подписчики')
                    ->columnSpan(6)
                    ->values(['CutCode' => 10000, 'Apple' => 9999]),

                LineChartMetric::make('Заказы')
                    ->line([
                        'Выручка 1' => [
                            now()->format('Y-m-d') => 100,
                            now()->addDay()->format('Y-m-d') => 200
                        ]
                    ])
                    ->line([
                        'Выручка 2' => [
                            now()->format('Y-m-d') => 300,
                            now()->addDay()->format('Y-m-d') => 400
                        ]
                    ], '#EC4176')
                    ->columnSpan(6),
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
