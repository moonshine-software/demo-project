<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Dictionary;

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Pages\Crud\IndexPage;

class DictionaryIndexPage extends IndexPage
{
    public function components(): array
	{
        $this->validateResource();

        return array_merge(
            $this->topLayer(),
            $this->mainLayer(),
            $this->bottomLayer(),
        );
	}
}
