<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Article;
use App\Models\Comment;
use MoonShine\Advanced\Components\Tabs\AsyncTab;
use MoonShine\Advanced\Components\Tabs\AsyncTabs;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Text;

#[SkipMenu]
class Dashboard extends Page
{
    protected function assets(): array
    {
        return [
            ...DonutChartMetric::make('')->getAssets(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
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
        if($request->has('date')) {
            return MoonShineJsonResponse::make()->html(['.async-table' => (string) $this->table()]);
        }

        return MoonShineJsonResponse::make()->html(
            (string) Div::make([
                FormBuilder::make()
                    ->asyncMethod('tableWithForm')
                    ->name('table-form')
                    ->fields([
                        Flex::make([
                            DateRange::make('Date')->required()->withoutWrapper(),
                            ActionButton::make('Apply')->dispatchEvent([AlpineJs::event(JsEvent::FORM_SUBMIT, 'table-form')]),
                        ]),
                    ])
                    ->hideSubmit(),

                Divider::make(),

                Div::make([
                    $this->table(),
                ])->class('async-table'),
            ]),
        );
    }

    private function table(): TableBuilder
    {
        return TableBuilder::make()
            ->fields([
                Text::make('IP'),
                Text::make('Email'),
                Text::make('Name'),
                Text::make('City'),
            ])
            ->simple()
            ->items([
                ['ip' => fake()->ipv4(), 'email' => fake()->email(), 'name' => fake()->name(), 'city' => fake()->city()],
                ['ip' => fake()->ipv4(), 'email' => fake()->email(), 'name' => fake()->name(), 'city' => fake()->city()],
                ['ip' => fake()->ipv4(), 'email' => fake()->email(), 'name' => fake()->name(), 'city' => fake()->city()],
            ]);
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
