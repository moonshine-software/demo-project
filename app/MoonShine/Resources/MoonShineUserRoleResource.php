<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use MoonShine\Attributes\Icon;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\ModelResource;

#[Icon('heroicons.outline.bookmark')]
class MoonShineUserRoleResource extends ModelResource
{
    public string $model = MoonshineUserRole::class;

    public string $column = 'name';

    protected bool $isAsync = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $withPolicy = true;

    public function title(): string
    {
        return __('moonshine::ui.resource.role');
    }

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable()->showOnExport(),
                Text::make(__('moonshine::ui.resource.role_name'), 'name')
                    ->required()
                    ->showOnExport(),
            ]),
        ];
    }

    /**
     * @return array{name: string}
     */
    public function rules($item): array
    {
        return [
            'name' => 'required|min:5',
        ];
    }

    public function import(): ?ImportHandler
    {
        return null;
    }

    public function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
