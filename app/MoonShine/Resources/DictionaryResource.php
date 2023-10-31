<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Dictionary;
use App\MoonShine\Pages\Dictionary\DictionaryDetailPage;
use App\MoonShine\Pages\Dictionary\DictionaryFormPage;
use App\MoonShine\Pages\Dictionary\DictionaryIndexPage;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Decorations\Block;
use MoonShine\Enums\ClickAction;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class DictionaryResource extends ModelResource
{
    protected string $model = Dictionary::class;

    protected string $title = 'Dictionary';

    protected bool $withPolicy = true;

    protected bool $isAsync = true;

    protected bool $createInModal = true;

    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public function fields(): array
    {
        return [
            Block::make([
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
            ])
        ];
    }

    public function pages(): array
    {
        return [
            DictionaryIndexPage::make($this->title()),
            DictionaryFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            DictionaryDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function rules(Model $item): array
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

