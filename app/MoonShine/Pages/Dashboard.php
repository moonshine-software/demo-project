<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Article;
use App\Models\Comment;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

#[SkipMenu]
class Dashboard extends Page
{
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function components(): array
	{
		return [
            Heading::make('Welcome to MoonShine!', 1),

            Heading::make('Demo version', 1),

            LineBreak::make(),

            Grid::make([
                Column::make([
                    ValueMetric::make('Articles')
                        ->value(Article::query()->count()),
                ])->columnSpan(6),

                Column::make([
                    ValueMetric::make('Comments')
                        ->value(Comment::query()->count()),
                ])->columnSpan(6),

                Column::make([
                    DonutChartMetric::make('Подписчики')
                        ->columnSpan(6)
                        ->values(['CutCode' => 10000, 'Apple' => 9999]),
                ])->columnSpan(6),

                Column::make([
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
                        ], '#EC4176'),
                ])->columnSpan(6)
            ]),
        ];
	}
}
