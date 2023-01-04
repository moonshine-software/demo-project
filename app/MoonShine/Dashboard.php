<?php

namespace App\MoonShine;

use App\Models\Article;
use App\MoonShine\Resources\ArticleResource;
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
                    ->value(Article::query()->count()),
            ]),
            DashboardBlock::make([
                ResourcePreview::make(
                    new ArticleResource(),
                    'Latest articles',
                    Article::query()
                        ->with(['author'])
                        ->latest()
                        ->limit(2)
                )
            ])
        ];
	}
}
