@extends('layouts.app')

@section('title', $dictionary->title)

@section('content')
    <div>
        <div class="prose
            prose-img:rounded-xl
            prose-pre:bg-purple
            prose-pre:text-white
            prose-code:bg-purple
            prose-code:text-white
            prose-invert"
        >
            <h1>{{ $dictionary->title }}</h1>

            {!! $dictionary->description !!}
        </div>
    </div>
@endsection
