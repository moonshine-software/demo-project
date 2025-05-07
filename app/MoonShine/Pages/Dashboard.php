<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Article;
use App\Models\Comment;
use App\MoonShine\Sets\DashboardTableWithForm;
use MoonShine\Advanced\Components\Tabs\AsyncTab;
use MoonShine\Advanced\Components\Tabs\AsyncTabs;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
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
    protected function assets(): array
    {
        return [
            ...DonutChartMetric::make('')->getAssets(),
        ];
    }

    public function getTitle(): string
    {
        return __('moonshine::ui.dashboard');
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
                    AsyncTabs::make([
                        AsyncTab::make('Metrics', $this->getRouter()->getEndpoints()->method('metrics')),
                        AsyncTab::make('Table', $this->getRouter()->getEndpoints()->method('tableWithForm')),
                    ]),
                ])->columnSpan(12),
            ]),
        ];
    }

    public function tableWithForm(MoonShineRequest $request): MoonShineJsonResponse
    {
        $set = new DashboardTableWithForm();

        if ($request->has('date')) {
            return MoonShineJsonResponse::make()->html([
                '.async-table' => (string)$set->table(),
            ]);
        }

        return MoonShineJsonResponse::make()->html(
            (string)$set->form('tableWithForm'),
        );
    }

    public function metrics(): MoonShineJsonResponse
    {
        return MoonShineJsonResponse::make()->html(
            (string)Grid::make([
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
                                now()->addDay()->format('Y-m-d') => 200,
                                now()->addDays(2)->format('Y-m-d') => 500,
                            ],
                        ])
                        ->line([
                            'Выручка 2' => [
                                now()->format('Y-m-d') => 300,
                                now()->addDay()->format('Y-m-d') => 400,
                                now()->addDays(2)->format('Y-m-d') => 300,
                            ],
                        ], '#EC4176')
                        ->line([
                            'Выручка 3' => [
                                now()->format('Y-m-d') => 400,
                                now()->addDay()->format('Y-m-d') => 500,
                                now()->addDays(2)->format('Y-m-d') => 300,
                            ],
                        ], '#1e96fc'),
                ])->columnSpan(6),
            ]),
        );
    }
}
