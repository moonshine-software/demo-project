<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Dictionary;

use MoonShine\ChangeLog\Components\ChangeLog;
use MoonShine\Decorations\Heading;
use MoonShine\Pages\Crud\FormPage;

class DictionaryFormPage extends FormPage
{
    public function topLayer(): array
    {
        return [
            Heading::make('Custom top'),

            ...parent::topLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
            ChangeLog::make('Changelog', $this->getResource())
        ];
    }
}
