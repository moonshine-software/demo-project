<?php

declare(strict_types=1);

namespace App\MoonShine\AbstractResources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Resources\Resource;

abstract class SeparateResource extends Resource
{
    abstract public function indexFields(): array;

    abstract public function formFields(): array;

    abstract public function detailFields(): array;

    public function rules(Model $item): array
    {
        return [];
    }

    public function fields(): array
    {
        if ($this->isNowOnIndex()) {
            return $this->indexFields();
        }

        if ($this->isNowOnForm()) {
            return $this->formFields();
        }

        if ($this->isNowOnDetail()) {
            return $this->detailFields();
        }

        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [];
    }

    public function search(): array
    {
        return ['id'];
    }
}
