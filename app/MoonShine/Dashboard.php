<?php

namespace App\MoonShine;

use App\Models\Article;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Dashboard\DashboardScreen;
use Leeto\MoonShine\Metrics\ValueMetric;

class Dashboard extends DashboardScreen
{
	public function blocks(): array
	{
		return [
            DashboardBlock::make([
                ValueMetric::make('Articles')
                    ->value(Article::query()->count())
            ])
        ];
	}
}
