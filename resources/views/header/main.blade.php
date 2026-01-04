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


        <div class="pr-4 flex ">
            <div class="flex gap-2">
                <button data-theme="dark">
                    <svg id="theme-toggle-dark-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>

                </button>

                <button data-theme="normal">
                    <svg id="theme-toggle-light-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <button data-theme="system">

                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    </svg>

                </button>
            </div>
        </div>
        <div class="pr-4 pt-2">

            <button @click="navOpen = ! navOpen" role="button" type="button" aria-controls="nav-list"
                aria-controls="navbar-hamburger" aria-expanded="false">
                <x-icons.hamburger />
            </button>

        </div>

    </div>





</div>

<template x-if="navOpen">
    <x-nav.main />
</template>
</div>
