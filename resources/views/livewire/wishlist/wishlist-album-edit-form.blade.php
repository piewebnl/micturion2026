<div>
    <header class="mb-6">
        <h1 class="text-3xl text-primary">
            @if ($wishlistAlbum->album->name)
                {{ $wishlistAlbum->album->name }}
            @else
                New wishlist album
            @endif
        </h1>
        <a href="{{ route('admin.wishlist') }}">Back to wishlist albums</a>
    </header>


    @if (!empty($messages))
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    @endif

    <form wire:submit.prevent="submit" x-data="{ submitForm() { this.$refs.submitButton.click(); } }" class="max-w-lg">

        @method('post')

        <form wire:submit.prevent="search" onkeydown="return event.key != 'Enter';" wire:change="search"
            wire:keydown.debounce.300ms="search" x-data="{ searchOpen: false }" class="max-w-full">
            <x-forms.select-search :wireModel="'form.persistent_album_id'" :options="$formData['albums']" fieldName="persistent_album_id"
                label="Or pick a new album" />
        </form>
        <div
            class="fixed bottom-0 left-0 z-20 flex w-full justify-between bg-zinc-300 bg-opacity-80 p-4 dark:bg-zinc-700">

            <x-buttons.button wire:click="submitAndRedirect" class="btn-primary">Save</x-buttons.button>

            <button type="submit" class="hidden" x-ref="submitButton"></button>

            @if ($wishlistAlbum->id)
                <x-buttons.button
                    wire:click="$dispatch('openModal', {
                component: 'modals.confirmation-modal',
                arguments: {
                    title: 'Delete concert?',
                    dispatch: 'deleteMethod'
                }
            })"
                    class="btn-secondary">
                    Delete</x-buttons.button>
            @endif
        </div>

    </form>
</div>
