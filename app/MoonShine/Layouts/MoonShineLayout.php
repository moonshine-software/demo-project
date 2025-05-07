<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Components\DemoVersionComponent;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Laravel\Layouts\CompactLayout;
use MoonShine\UI\Components\{Components,
    Layout\Body,
    Layout\Content,
    Layout\Div,
    Layout\Flash,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Wrapper};

final class MoonShineLayout extends CompactLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make(
                <<<'Style'
                    :root {
                      --radius: 0.1rem;
                      --radius-sm: 0.075rem;
                      --radius-md: 0.175rem;
                      --radius-lg: 0.25rem;
                      --radius-xl: 0.3rem;
                      --radius-2xl: 0.4rem;
                      --radius-3xl: 0.6rem;
                      --radius-full: 9999px;
                    }
                    Style,
            ),
        ];
    }

    protected function menu(): array
    {
        return $this->autoloadMenu();
    }

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Wrapper::make([
                        $this->getSidebarComponent(),

                        Div::make([
                            DemoVersionComponent::make(),

                            Flash::make(),

                            $this->getHeaderComponent(),

                            Content::make([
                                Components::make(
                                    $this->getPage()->getComponents(),
                                ),
                            ]),

                            $this->getFooterComponent(),
                        ])->class('layout-page'),
                    ]),
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
