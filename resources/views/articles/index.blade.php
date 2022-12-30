@extends('layouts.app')

@section('title', 'Статьи')

@section('content')
    <x-title>Статьи</x-title>

    @if($articles->isNotEmpty())
        <div class="tasks grid gap-4 grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-14 xl:gap-y-20 mt-12 md:mt-20">
            @foreach($articles as $article)
                @include('articles.shared.item', ['item' => $article])
            @endforeach
        </div>
    @else
        <div class="text-[26px] sm:text-xl xl:text-[48px] 2xl:text-2xl font-black my-8">
            Пока таких записей нет :( <span class="text-pink">Но скоро добавим</span>
        </div>
    @endif

    {{ $articles->links() }}

@endsection
