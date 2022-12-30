<div class="tasks-card flex flex-col rounded-3xl md:rounded-[40px] bg-card">
    <div class="tasks-card-photo overflow-hidden h-40 xs:h-48 sm:h-[280px] rounded-3xl md:rounded-[40px]">
        <a
            @if(isset($href)) href="{{ $href }}" @endif
            @if(isset($click)) @click="{{ $click }}" @endif
        >
            <img src="{{ asset($cover) }}"
                 class="object-cover w-full h-full"
                 alt="{{ $title }}">
        </a>
    </div>
</div>
