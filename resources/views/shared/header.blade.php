<header class="header pt-6 xl:pt-12">
    <div class="container">
        <div class="header-inner flex items-center">
            <div class="header-logo shrink-0">
                <a href="{{ route('home') }}" rel="home">
                <img src="{{ asset('images/logo.svg') }}"
                     class="w-[148px] md:w-[201px] h-[36px] md:h-[50px]"
                     alt="CutCode"
                >
                </a>
            </div><!-- /.header-logo -->

            <div class="header-menu grow flex justify-start ml-8 mr-8">
                <nav class="inline-block hidden lg:block">
                    <a href="{{ route('articles.index') }}" class="ml-4 mr-4 text-{{ request()->routeIs('articles.*') ? 'pink' : 'white' }} hover:text-pink font-bold">
                        Блог
                    </a>
                </nav>
            </div><!-- /.header-menu -->


            <div class="header-actions flex items-center">
                <button id="burgerMenu" class="flex lg:hidden text-white hover:text-pink transition">
                    <span class="sr-only">Меню</span>
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div><!-- /.header-actions -->
        </div><!-- /.header-inner -->
    </div><!-- /.container -->
</header>
