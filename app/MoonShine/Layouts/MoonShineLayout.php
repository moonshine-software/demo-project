<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Components\DemoVersionComponent;
use MoonShine\ColorManager\Palettes\NeutralPalette;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Title;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = NeutralPalette::class;

    protected function menu(): array
    {
        return $this->autoloadMenu();
    }

    protected function getContentComponents(): array
    {
        $components = [
            Components::make([
                ...$this->getPage()->getComponents(),
            ]),
        ];

        if ($this->withTitle()) {
            $hasSubtitle = $this->withSubTitle() && $this->getPage()->getSubtitle() !== '';

            return array_filter([
                DemoVersionComponent::make(),
                Title::make($this->getPage()->getTitle())->class($hasSubtitle ? '' : 'mb-6'),
                $hasSubtitle ? Heading::make($this->getPage()->getSubtitle())->class('mb-6') : null,
                ...$components,
            ]);
        }

        return $components;
    }

}
