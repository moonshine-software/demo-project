<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Dictionary;

use App\MoonShine\Pages\Dictionary\DictionaryDetailPage;
use App\MoonShine\Pages\Dictionary\DictionaryFormPage;
use App\MoonShine\Pages\Dictionary\DictionaryIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\ClickAction;
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

    /**
     * @param  Dictionary  $item
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:1'],
            'slug' => ['required', 'string', 'min:1'],
            'description' => ['required', 'string', 'min:1'],
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Title'),
        ];
    }
}

