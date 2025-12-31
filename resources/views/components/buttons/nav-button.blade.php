@props(['route', 'routeName' => 'cookbooks', 'label' => 'Label', 'icon' => 'icons.menu'])

<div {{ $attributes->merge(['class' => '']) }}>
    <a href="{{ route($route) }}"
        class="flex flex-col items-center hover:no-underline hover:text-violet-500 hover:fill-violet-500  hover:stroke-violet-500 {{ request()->is($routeName) ? 'text-violet-500 stroke-violet-500 fill-violet-500' : 'fill-stone-500 stroke-stone-500 text-stone-500' }} " />
    <x-dynamic-component :component="$icon" />
    <span class="hidden sm:block">{{ $label }}</span>
    </a>
</div>
