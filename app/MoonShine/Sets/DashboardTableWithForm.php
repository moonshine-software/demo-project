<?php

declare(strict_types=1);

namespace App\MoonShine\Sets;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Text;

final readonly class DashboardTableWithForm
{
    public function form(string $method): ComponentContract
    {
        return Div::make([
            FormBuilder::make()
                ->asyncMethod($method)
                ->name('table-form')
                ->fields([
                    Flex::make([
                        DateRange::make('Date')
                            ->required()
                            ->withoutWrapper(),

                        ActionButton::make('Apply')->dispatchEvent([
                            AlpineJs::event(JsEvent::FORM_SUBMIT, 'table-form')
                        ]),
                    ])->unwrap(),
                ])
                ->hideSubmit(),

            Divider::make(),

            Div::make([
                $this->table(),
            ])->class('async-table'),
        ]);
    }

    public function table(): TableBuilder
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
}
