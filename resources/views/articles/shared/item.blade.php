<div class="tasks-card flex flex-col rounded-3xl md:rounded-[40px] bg-card">
    <div class="tasks-card-photo overflow-hidden h-40 xs:h-48 sm:h-[280px] rounded-3xl md:rounded-[40px]">
        <a href="{{ route('articles.show', $item) }}">
            <img src="{{ $item->getThumbnail('thumbnail', 'fit', '500x300', 'articles') }}"
                 class="object-cover w-full h-full"
                 alt="{{ $item->title }}">
        </a>
    </div>
    <div class="grow flex flex-col pt-6 sm:pt-10 pb-6 sm:pb-10 2xl:pb-14 px-5 sm:px-8 2xl:px-12">
        <h3 class="text-md md:text-lg 2xl:text-xl font-black">
            {{ $item->title }}
        </h3>

        <div class="mt-auto">
            @include('articles.shared.categories', ['categories' => $item->categories])

            <div class="flex flex-wrap sm:items-center justify-center sm:justify-between mt-8 sm:mt-10">
                <x-button href="{{ route('articles.show', $item) }}">
                    Подробнее
                </x-button>

                <div class="mt-5 sm:mt-0 text-center sm:text-right">
                    @if($item->author_id && $item->author_id !== 1)
                        @include('articles.shared.author', [
                            'author' => $item->author,
                            'date' => $item->created_at
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
