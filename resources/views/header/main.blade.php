<div class="flex w-full items-center justify-between gap-4 bg-zinc-200 p-4 dark:bg-zinc-800">
    <div class="pl-2 text-3xl sm:text-4xl">
        <a href="/"
            class="font-rock-salt text-amber-600 hover:text-amber-700 hover:no-underline dark:text-amber-600 dark:hover:text-amber-500">{{ config('app.name', 'Micturion') }}</a>
    </div>
    <div class="flex flex-row flex-wrap justify-end gap-4">

        <div class="flex flex-row flex-wrap">
            @auth
                <a href="{{ url('/profile') }}"
                    class="elative inline-flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-600">
                    <div class="font-medium text-zinc-600 dark:text-zinc-300">
                        {{ Auth::user()->avatarName }}
                    </div>
                </a>
            @else
                @if (!request()->is('login'))
                    <a href="{{ route('login') }}" class="btn-primary">
                        Login</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">
                        Register</a>
                @endif
            @endauth
        </div>



        <button type="button" x-on:click="themeMenu =!themeMenu"
            class="rounded-lg p-2.5 text-sm text-zinc-500 hover:bg-zinc-100 focus:outline-none focus:ring-4 focus:ring-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:focus:ring-zinc-700">
            <svg id="theme-dark-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg id="theme-light-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    fill-rule="evenodd" clip-rule="evenodd"></path>
            </svg>
        </button>



        <div class="pr-4 pt-2">

            <button @click="navOpen = ! navOpen" role="button" type="button" aria-controls="nav-list"
                aria-controls="navbar-hamburger" aria-expanded="false">
                <x-icons.hamburger />
            </button>

        </div>

    </div>

</div>

<div x-show="themeMenu" x-cloak>
    <div class="absolute right-2 top-[78px] z-10 flex flex-col items-stretch gap-2" id="theme-toggle-menu">
        <div id="theme-set-dark" class="hidden">
            <button class="btn w-full bg-zinc-100 dark:bg-zinc-800" x-on:click="themeMenu = false">
                <svg id="theme-toggle-dark-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                Dark
            </button>
        </div>
        <div id="theme-set-light" class="hidden">
            <button class="btn w-full bg-zinc-100 dark:bg-zinc-800" x-on:click="themeMenu = false">
                <svg id="theme-toggle-light-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>Light</button>
        </div>
        <div id="theme-set-system">
            <button class="btn w-full bg-zinc-100 dark:bg-zinc-800" x-on:click="themeMenu = false">
                <svg class="h-6 w-6" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4Zm16 7H4v7h16v-7ZM5 8a1 1 0 0 1 1-1h.01a1 1 0 0 1 0 2H6a1 1 0 0 1-1-1Zm4-1a1 1 0 0 0 0 2h.01a1 1 0 0 0 0-2H9Zm2 1a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2H12a1 1 0 0 1-1-1Z"
                        clip-rule="evenodd" />
                </svg>
                System</button>
        </div>
    </div>
</div>

<template x-if="navOpen">
    <x-nav.main />
</template>
</div>
