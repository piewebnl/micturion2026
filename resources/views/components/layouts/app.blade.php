<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'Laravel') }}">
    <meta name="robots" content="noindex">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script defer src="https://cdn.jsdelivr.net/npm/@imacrayon/alpine-ajax@0.7.0/dist/cdn.min.js"></script>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @livewireStyles

    @filamentStyles

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>


</head>


<body x-data="{ navOpen: false, loading: false, themeMenu: false }">

    <div @click="navOpen = false" :class="{ 'hidden': !navOpen }"
        class="fixed inset-0 z-50 h-full w-full bg-zinc-900 opacity-90"></div>

    @livewire('wire-elements-modal')
    @include('header.main')

    <x-messages.flash />

    @if (!empty($messages))
        @foreach ((array) $messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    @endif

    <main class="p-2 pb-24 md:p-8">
        {{ $slot }}
    </main>

    @persist('toaster')
    @endpersist
    @livewireScripts
    @filamentScripts
</body>

</html>
