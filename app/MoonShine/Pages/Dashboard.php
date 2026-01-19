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
use MoonShine\Apexcharts\Components\SparklineChartMetric;
use MoonShine\Apexcharts\Support\SeriesItem;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\Support\Attributes\AsyncMethod;
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
                    SparklineChartMetric::make('Revenue')
                        ->values([30, 40, 35, 50, 49, 60, 70, 91, 125])
                        ->value('192.10k', prefix: '$')
                        ->change(32, suffix: 'k')
                        ->colors(['#10b981']),
                ])->columnSpan(4),

                Column::make([
                    SparklineChartMetric::make('Expenses')
                        ->values([100, 95, 90, 85, 80])
                        ->value('45.5k', prefix: '$')
                        ->change(
                            -12,
                            suffix: 'k',
                        )
                        ->colors(['#ef4444']),
                ])->columnSpan(4),

                Column::make([
                    SparklineChartMetric::make('Posts')
                        ->values([30, 40, 35, 50, 49, 60, 70, 91, 125])
                        ->value('200')
                        ->change(2)
                        ->colors(['#3b82f6']),
                ])->columnSpan(4),

                Column::make([
                    AsyncTabs::make([
                        AsyncTab::make('Metrics', $this->getRouter()->getEndpoints()->method('metrics')),
                        AsyncTab::make('Table', $this->getRouter()->getEndpoints()->method('tableWithForm')),
                    ]),
                ])->columnSpan(12),
            ]),
        ];
    }

    #[AsyncMethod]
    public function tableWithForm(CrudRequestContract $request): JsonResponse
    {
        $set = new DashboardTableWithForm();

        if ($request->has('date')) {
            return JsonResponse::make()->html([
                '.async-table' => (string)$set->table(),
            ]);
        }

        return JsonResponse::make()->html(
            (string)$set->form('tableWithForm'),
        );
    }

    #[AsyncMethod]
    public function metrics(): JsonResponse
    {
        return JsonResponse::make()->html(
            (string)Grid::make([
                Column::make([
                    DonutChartMetric::make('Подписчики')
                        ->columnSpan(6)
                        ->values(['CutCode' => 10000, 'Apple' => 9999]),
                ])->columnSpan(6),
                Column::make([
                    LineChartMetric::make('Заказы')
                        ->series(SeriesItem::make('Выручка 1', [
                            now()->format('Y-m-d') => 100,
                            now()->addDay()->format('Y-m-d') => 200,
                            now()->addDays(2)->format('Y-m-d') => 500,
                        ])->line())
                        ->series(SeriesItem::make('Выручка 2', [
                            now()->format('Y-m-d') => 300,
                            now()->addDay()->format('Y-m-d') => 400,
                            now()->addDays(2)->format('Y-m-d') => 300,
                        ])->line())
                        ->series(SeriesItem::make('Выручка 3', [
                            now()->format('Y-m-d') => 400,
                            now()->addDay()->format('Y-m-d') => 500,
                            now()->addDays(2)->format('Y-m-d') => 300,
                        ])->line())
                ])->columnSpan(6),
            ]),
        );
    }
}
