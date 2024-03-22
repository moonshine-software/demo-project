<?php

declare(strict_types=1);

namespace App\MoonShine;

use App\MoonShine\Components\DemoVersionComponent;
use MoonShine\Components\Layout\{Content,
    Flash,
    Footer,
    Header,
    LayoutBlock,
    LayoutBuilder,
    Menu,
    Profile,
    Search,
    Sidebar};
use MoonShine\Contracts\MoonShineLayoutContract;

final class MoonShineLayout implements MoonShineLayoutContract
{
    public static function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Sidebar::make([
                Menu::make(),
                Profile::make(withBorder: true),
            ]),
            LayoutBlock::make([
                DemoVersionComponent::make(),
                Flash::make(),
                Header::make([
                    Search::make()
                ]),
                Content::make(),
                Footer::make()
                    ->copyright(fn(): string => sprintf(
                        <<<'HTML'
                            &copy; 2021-%d Made with ❤️ by
                            <a href="https://cutcode.dev"
                                class="font-semibold text-primary hover:text-secondary"
                                target="_blank"
                            >
                                CutCode
                            </a>
                        HTML,
                        now()->year
                    ))
                    ->menu([
                        'https://github.com/moonshine-software/moonshine' => 'GitHub',
                        'https://github.com/moonshine-software/demo-project' => 'Demo project',
                        'https://moonshine-laravel.com' => 'Documentation',
                    ]),
            ])->customAttributes(['class' => 'layout-page']),
        ]);
    }
}
