@props(['countFiltersUsed', 'indicatorOnly' => false])

@if ($countFiltersUsed > 0)
    @if ($indicatorOnly)
        <span class="rounded-full bg-amber-600 px-2 text-zinc-200 dark:text-zinc-300">{{ $countFiltersUsed }}</span>
    @else
        <div class="my-4">
            Filters:
            <span class="rounded-full bg-amber-600 px-2 text-zinc-200 dark:text-zinc-300">{{ $countFiltersUsed }}</span>
        </div>
    @endif
@endif
