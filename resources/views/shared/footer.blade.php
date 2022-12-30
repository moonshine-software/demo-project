<footer class="footer py-8 sm:py-12 xl:py-16">
    <div class="container">
        <div class="flex flex-wrap lg:flex-nowrap items-center">
            <div class="footer-logo order-0 basis-full sm:basis-1/2 lg:basis-1/3 shrink-0 text-center sm:text-left">
                <a href="{{ route('home') }}" class="inline-block" rel="home">
                <img src="{{ asset('images/logo.svg') }}" class="w-[155px] h-[38px]" alt="CutCode">
                </a>
            </div><!-- /.footer-logo -->

            <div class="footer-copyright order-2 lg:order-1 basis-full lg:basis-1/3 mt-8 lg:mt-0">
                <div class="text-[#999] text-xxs xs:text-xs sm:text-sm text-center">
                    CutCode, {{ now()->year }} © Все права защищены.
                </div>

                @if(false)
                <ul class="flex flex-col md:flex-row justify-between gap-3 md:gap-4 mt-2">
                    <li>
                        <a href="#" class="inline-block text-white hover:text-white/70 text-xxs md:text-xs font-medium" target="_blank" rel="noopener">Пользовательское соглашение</a>
                    </li>
                </ul>
                @endif
            </div><!-- /.footer-copyright -->

            <div class="footer-social order-1 lg:order-2 basis-full sm:basis-1/2 lg:basis-1/3 mt-8 sm:mt-0">
                <div class="flex flex-wrap items-center justify-center sm:justify-end space-x-6">
                    <a href="https://youtube.com/c/CutCodeRu" class="inline-flex items-center text-white hover:text-pink" target="_blank" rel="nofollow noopener">
                        <img class="h-5 lg:h-6" src="{{ asset('images/icons/youtube.svg') }}" alt="YouTube">
                        <span class="ml-2 lg:ml-3 text-xxs font-semibold">YouTube</span>
                    </a>
                    <a href="https://t.me/laravel_chat" class="inline-flex items-center text-white hover:text-pink" target="_blank" rel="nofollow noopener">
                        <img class="h-5 lg:h-6" src="{{ asset('images/icons/telegram.svg') }}" alt="Telegram">
                        <span class="ml-2 lg:ml-3 text-xxs font-semibold">Telegram</span>
                    </a>
                </div>
            </div><!-- /.footer-social -->
        </div>
    </div><!-- /.container -->
</footer>

<div id="mobileMenu" class="hidden bg-white fixed inset-0 z-[9999]">
    <div class="container">
        <div class="mmenu-heading flex items-center pt-6 xl:pt-12">
            <div class="shrink-0 grow">
                <a href="{{ route('home') }}" rel="home">
                    <img src="{{ asset('images/logo-dark.svg') }}" class="w-[148px] md:w-[201px] h-[36px] md:h-[50px]" alt="CutCode">
                </a>
            </div>
            <div class="shrink-0 flex items-center">
                <button id="closeMobileMenu" class="text-dark hover:text-purple transition">
                    <span class="sr-only">Закрыть меню</span>
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div><!-- /.mmenu-heading -->

        <div class="mmenu-inner pt-10">
            <nav class="flex flex-col mt-8">
                <a href="{{ route('articles.index') }}" class="self-start py-1 text-dark hover:text-pink text-md font-bold">Блог</a>
            </nav>
        </div><!-- /.mmenu-inner -->
    </div><!-- /.container -->
</div>
