@extends('layouts.app')

@section('title', 'CutCode - проект по обучению Laravel, PHP и JS')
@section('description', 'Видеоуроки и курсы по разработке проектов на Laravel, PHP и JS - это комьюнити CutCode')

@section('content')
    <div x-data="home">
        <div  class="tasks grid gap-4 grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-14 xl:gap-y-20 mt-12 md:mt-20">

            @include('shared.card', [
                'href' => 'https://www.youtube.com/c/CutCodeRu?sub_confirmation=1',
                'title' => 'YouTube CutCode',
                'cover' => 'images/projects/youtube.jpg'
            ])

            @include('shared.card', [
                'href' => 'https://solid.cutcode.ru',
                'title' => 'SOLID Code by CutCode',
                'cover' => 'images/projects/solid.jpg'
            ])

            @include('shared.card', [
                'href' => 'https://livewire.cutcode.ru',
                'title' => 'LiveWire Components and Code examples',
                'cover' => 'images/projects/livewire.jpg'
            ])

            @include('shared.card', [
                'href' => 'https://moonshine.cutcode.ru',
                'title' => 'Laravel Dashboard by CutCode',
                'cover' => 'images/projects/moonshine.jpg'
            ])

            @include('shared.card', [
                'href' => 'https://t.me/laravel_cutcode',
                'title' => 'Telegram CutCode',
                'cover' => 'images/projects/telegram.jpg'
            ])

            @include('shared.card', [
                'click' => 'modal = true',
                'title' => 'Web development',
                'cover' => 'images/projects/development.jpg'
            ])
        </div>

        <div x-bind="backdrop" class="fixed inset-0 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title-1" style="">
            <div x-show="modal" x-transition.opacity="" class="fixed inset-0 bg-black bg-opacity-50" aria-hidden="true" style=""></div>

            <div x-show="modal" x-transition="" x-on:click="modal = false" class="relative min-h-screen flex items-center justify-center p-4" style="">

                <div x-on:click.stop="" x-trap.noscroll.inert="modal" class="relative text-white max-w-2xl w-full bg-purple rounded-xl shadow-lg p-12 overflow-y-auto">

                    <h2 class="text-xl font-bold" :id="$id('modal-title')" id="modal-title-1">Поможем с разработкой проекта любой сложности</h2>

                    <p class="mt-2 text-gray-600">
                        Пишите мне в telegram! Обсудим!
                    </p>

                    <div class="mt-8 flex space-x-2">

                        <a href="https://t.me/leeto_telegram" x-on:click="modal = false" class="bg-transparent border border-gray-200 rounded-md px-5 py-2.5">
                            Написать
                        </a>

                        <button type="button" x-on:click="modal = false" class="bg-pink border border-gray-200 rounded-md px-5 py-2.5">
                            Передумал :(
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script aria-hidden="true">
      document.addEventListener('alpine:init', () => {Alpine.data('home', () => ({ modal: false }))
        Alpine.bind('backdrop', () => ({ ['x-show']() { return this.modal }, ['x-on:keydown.escape.prevent.stop']() { this.modal = false }, ['role']: 'dialog', ['aria-modal']: 'true', ['x-id']() { return ['modal-title'] }, [':aria-labelledby']() { return this.$id('modal-title') }, }))
      })</script>
@endsection
