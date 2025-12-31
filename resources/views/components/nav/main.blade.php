<?php

use Livewire\Volt\Component;

new class extends Component {
    public function logout(): void
    {
        auth()->guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }
}; ?>
@php
    $firstAdminMenu = false;
@endphp

<div class="fixed left-0 top-0 z-50 h-screen w-3/4 overflow-y-auto bg-white p-4 transition-transform dark:bg-zinc-800 sm:w-1/2 md:w-1/3"
    tabindex="-1" aria-labelledby="drawer-navigation-label">
    <button type="button" aria-controls="drawer-navigation" @click="navOpen = false"
        class="absolute right-2.5 top-2.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-zinc-400 hover:bg-zinc-200 hover:text-zinc-900 dark:hover:bg-zinc-600 dark:hover:text-white">
        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
        <span class="sr-only">Close</span>
    </button>
    <div class="mt-4 overflow-y-auto py-4">
        <ul class="space-y-2 font-medium">
            @foreach ($menus as $menu)
                @if ($menu->auth and auth()->check() or !$menu->auth)
                    <li>
                        @if ($menu->auth && !$firstAdminMenu)
                            <div class="mb-2 mt-4 text-xl"><a href="/admin">Admin</a></div>
                            @php $firstAdminMenu = true; @endphp
                        @endif
                        <a href="{{ $menu->url }}"
                            class="cursor-pointer text-2xl text-amber-600 hover:text-amber-700 hover:no-underline dark:text-amber-600 dark:hover:text-amber-500">{{ $menu->name }}</a>
                    </li>
                @endif
            @endforeach

            @auth
                <div x-data="{ submitForm() { this.$refs.flushCacheForm.submit() } }">
                    <form action="/admin/flush-cache" method="post" x-ref="flushCacheForm">
                        <li x-on:click="submitForm()">
                            @csrf
                            <span
                                class="cursor-pointer text-2xl text-amber-600 hover:text-amber-700 hover:no-underline dark:text-amber-600 dark:hover:text-amber-500">Flush
                                cache</span>

                        </li>
                    </form>
                </div>
                <li>
                    @livewire('actions.logout')
                </li>
            @else
                <li>
                    <a href="{{ route('login') }}"
                        class="cursor-pointer text-2xl text-amber-600 hover:text-amber-700 hover:no-underline dark:text-amber-600 dark:hover:text-amber-500">
                        Login</a>
                </li>
            @endauth
        </ul>
    </div>
</div>
