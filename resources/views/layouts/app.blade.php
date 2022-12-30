<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', env('APP_NAME'))</title>
    <meta name="description" content="@yield('description', env('APP_NAME'))">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#7843E9">
    <meta name="msapplication-TileColor" content="#7843E9">
    <meta name="theme-color" content="#7843E9">

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/main.css') }}" rel="stylesheet">
    <script src="{{ mix('/js/app.js') }}" defer></script>
</head>
<body class="antialiased">
@include('shared.alert')
@include('shared.header')

<main class="py-16 lg:py-20">
    <div class="container">
        @yield('content')
    </div>
</main>

@include('shared.footer')

<div class="aspect-video"></div>
</body>
</html>
<?php
