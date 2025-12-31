<x-modal.modal>

    <div class="p-6 text-center">
        <svg class="mx-auto mb-6 h-12 w-12 text-gray-400 dark:text-gray-200" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>

        <h1 class="mb-5 text-2xl font-normal text-gray-500 dark:text-gray-400">{{ $modal['title'] }}</h1>
        @if ($modal['message'])
            <p class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ $modal['message'] }}</p>
        @endif

        <button wire:click="confirm" class="btn-primary" data-modal-hide="popup-modal" type="button"
            class="btn-primary">{{ $modal['confirm_text'] }}
        </button>
        <button wire:click="decline" data-modal-hide="popup-modal" type="button"
            class="btn">{{ $modal['decline_text'] }}</button>
    </div>
</x-modal.modal>
