<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\Models\Comment;
use App\MoonShine\Components\DemoVersionComponent;
use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\DictionaryResource;
use App\MoonShine\Resources\SettingResource;
use App\MoonShine\Resources\UserResource;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Layouts\CompactLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\UI\Components\{Components,
    Layout\Div,
    Layout\Flash,
    Layout\Body,
    Layout\Content,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\TopBar,
    Layout\Wrapper};
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends CompactLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make(<<<'Style'
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
        Style
            ),
        ];
    }

    protected function menu(): array
    {
        return $this->autoloadMenu();
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    protected function getLogoComponent(): Logo
    {
        return parent::getLogoComponent();
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
                                    $this->getPage()->getComponents()
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
