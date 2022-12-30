@if($categories)
    <div class="flex flex-wrap gap-3 mt-7">
        @foreach($categories as $category)
            <div class="grow xs:grow-0 py-2 px-4 rounded-[32px] bg-[#2A2B4E]
                 text-white text-xxs sm:text-xs font-semibold whitespace-nowrap"
            >
                {{ $category->title }}
            </div>
        @endforeach
    </div>
@endif
