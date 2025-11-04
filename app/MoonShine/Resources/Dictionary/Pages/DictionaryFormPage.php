<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Dictionary\Pages;

use App\MoonShine\Resources\Dictionary\DictionaryResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use MoonShine\ChangeLog\Components\ChangeLog;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<DictionaryResource>
 */
class DictionaryFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Title')->required(),
                Slug::make('Slug')
                    ->unique()
                    ->separator('-')
                    ->from('title')
                    ->required(),
                TinyMce::make('Description'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'title' => ['required', 'string', 'min:3'],
            'slug' => ['required', 'string', 'min:3'],
            'description' => ['required', 'string', 'min:10'],
        ];
    }

    public function topLayer(): array
    {
        return [
            Heading::make('Custom top layer'),

            ...parent::topLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
            ChangeLog::make(
                'Changelog',
                $this->getResource(),
                userResource: MoonShineUserResource::class
            )
        ];
    }
}
