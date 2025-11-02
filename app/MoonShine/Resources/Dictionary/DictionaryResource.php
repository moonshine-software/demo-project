<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Dictionary;

use App\Models\Dictionary;
use App\MoonShine\Resources\Dictionary\Pages\DictionaryDetailPage;
use App\MoonShine\Resources\Dictionary\Pages\DictionaryFormPage;
use App\MoonShine\Resources\Dictionary\Pages\DictionaryIndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Dictionary, DictionaryIndexPage, DictionaryFormPage, DictionaryDetailPage>
 */
#[Icon('document-duplicate')]
#[Order(5)]
class DictionaryResource extends ModelResource
{
    protected string $model = Dictionary::class;

    protected string $title = 'Dictionary';

    protected bool $withPolicy = true;

    protected bool $createInModal = true;

    public function pages(): array
    {
        return [
            DictionaryIndexPage::class,
            DictionaryFormPage::class,
            DictionaryDetailPage::class,
        ];
    }
}

