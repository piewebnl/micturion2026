<x-layouts.app>

    <div class="p-4 sm:p-8">
        <ul class="max-w-8xl mx-auto flex flex-wrap items-center justify-evenly overflow-hidden p-0">
            @foreach ($menus as $menu)
                @if ($menu->homepage == 1 and !$menu->auth or $menu->auth and auth()->check())
                    <li class="aspect-1/2 relative m-4 flex w-[480px] list-none justify-center">
                        <a href="{{ $menu->url }}" class="flex flex-col shadow-lg hover:scale-105 hover:no-underline">
                            @if ($menu->menuImage)
                                <x-images.image type="menu" slug="{{ $menu->menuImage->slug }}" size="1000"
                                    largestWidth="{{ $menu->menuImage->largest_width }}" class="cover cursor-pointer"
                                    hash="{{ $menu->menuImage->hash }}"
                                    alt="{{ $menu->name }} - {{ $menu->name }} Album Cover" />
                                <div class="flex w-full justify-center">
                                    <span
                                        class="absolute bottom-0 left-0 flex min-h-20 w-full items-center justify-center bg-zinc-200 text-center font-rock-salt text-xl text-amber-600 opacity-90 dark:bg-zinc-900">{{ $menu->name }}</span>
                                </div>
                            @else
                                <span
                                    class="flex min-h-20 w-full items-center justify-center bg-zinc-900 text-center font-rock-salt text-xl text-amber-600 opacity-90">{{ $menu->name }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</x-layouts.app>
