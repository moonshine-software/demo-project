<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Dictionary;

use App\MoonShine\Pages\Dictionary\DictionaryDetailPage;
use App\MoonShine\Pages\Dictionary\DictionaryFormPage;
use App\MoonShine\Pages\Dictionary\DictionaryIndexPage;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Icon('document-duplicate')]
#[Order(5)]
class DictionaryResource extends ModelResource
{
    protected string $model = Dictionary::class;

    protected string $title = 'Dictionary';

    protected bool $withPolicy = true;

    protected bool $createInModal = true;

    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public function pages(): array
    {
        return [
            DictionaryIndexPage::class,
            DictionaryFormPage::class,
            DictionaryDetailPage::class,
        ];
    }

    public function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')
                ->updateOnPreview()
                ->required(),
            Slug::make('Slug')
                ->unique()
                ->separator('-')
                ->from('title')
                ->required(),
            TinyMce::make('Description'),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields()
            ])
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields()
        ];
    }

    public function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:1'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
        ];
    }

    public function search(): array
    {
        return ['id', 'title'];
    }

    public function filters(): array
    {
        return [
            Text::make('Title')
        ];
    }
}

