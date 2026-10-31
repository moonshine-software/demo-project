<div class="flex items-center space-x-4 grow xs:grow-0 py-2 px-4 rounded-[16px] bg-[#2A2B4E]
             text-white text-xxs sm:text-xs font-semibold whitespace-nowrap text-left">
    <img src="{{ $author->avatar ? Storage::url($author->avatar) : asset('vendor/moonshine/avatar.jpg') }}"
         class="shadow-lg rounded-full w-12 h-12 align-middle border-none m-0"
         alt="{{ $author->name }}"
    />

    <div>
        <div class="font-bold">{{ $author->name }}</div>
        <div class="text-xs font-normal">{{ $date->format('d.m.Y в H:i') }}</div>
    </div>
</div>
