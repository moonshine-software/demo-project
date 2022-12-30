@props([
    'href' => '#',
    'type' => 'pink',
    'full' => false,
    'active' => false
])

<a href="{{ $href }}"
   {{ $attributes->class([
        'w-full' => $full,
        'btn btn-pink' => $type === 'pink',
        'btn btn-outline' => $type === 'outline',
        'py-2 md:py-3 xl:py-4 px-3 md:px-6 xl:px-8 rounded-[32px] border border-[#696A7E] hover:border-pink text-white hover:text-pink text-xxs sm:text-xs lg:text-sm 2xl:text-md font-semibold whitespace-nowrap' => $type === 'transparent-pink',
        '_is-active' => $active,
    ]) }}
>
    {{ $slot }}
</a>
