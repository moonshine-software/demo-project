@extends('layouts.app')

@section('title', 'Словарь')

@section('content')
    <div>
        @foreach($dictionaries as $dictionary)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @include('dictionaries.shared.item', ['item' => $dictionary])
            </div>
        @endforeach
    </div>

    {{ $dictionaries->links() }}
@endsection
