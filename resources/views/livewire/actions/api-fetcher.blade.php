<div x-data="{
    percentage: @entangle('percentage'),
    checkClicked() {
        this.loading = !(this.percentage === 0 || this.percentage === 100);
    }
}" x-init="checkClicked" x-effect="checkClicked()">

    <button wire:click="fetchAll" x-on:click="loading = true" x-bind:disabled="loading"
        class="{{ $class }}">{{ $buttonText }}</button>


    @if ($percentage > 0 && $percentage < 100)
        <div class="my-4">
            <div class="mb-2 flex justify-between">
                <span
                    class="text-base font-medium text-blue-700 dark:text-white">{{ $progressBarTexts['fetching'] }}</span>
                <span class="text-sm font-medium text-blue-700 dark:text-white">{{ $percentage }}%</span>
            </div>
            <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                <div class="h-2.5 rounded-full bg-green-500" style="width: {{ $percentage }}%">&nbsp;</div>
            </div>
        </div>
    @endif
    @if ($percentage == 100)
        <div class="mb-4 mt-4 flex items-center rounded-lg bg-green-50 p-4 text-zinc-700 dark:bg-zinc-800 dark:text-green-400"
            role="alert">
            <x-icons.info />

            <div class="ms-3 font-medium">
                {{ $progressBarTexts['done'] }}
            </div>

        </div>
    @endif
    @livewire('actions.api-fetch', ['url' => $url, 'item' => $item, 'id' => $this->id])


</div>
