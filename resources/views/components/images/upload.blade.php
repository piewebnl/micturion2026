@props(['images' => [], 'wireModel' => 'images', 'multiple' => false, 'message'])


<div class="mb-6">
    @if ($images)
        <div class="flex flex-row">
            @foreach ($images as $image)
                <div class="max-w-xs">
                    <img src="{{ $image->temporaryUrl() }}">
                </div>
            @endforeach
        </div>
    @endif

    @error('images.*')
        <span class="error">{{ $message }}</span>
    @enderror

    <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false; redirect = false; submitForm();"
        x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">

        <label for="file-upload" class="custom-file-upload inline-flex">
            <span class="btn btn-secondary cursor-pointer">Add new image</span>
            <input type="file" wire:model="{{ $wireModel }}" {{ $multiple ? 'multiple' : '' }} class="mb-6"
                id="file-upload">
        </label>

        <div x-show="uploading" class="flex flex-row items-center gap-2">
            <progress max="100" x-bind:value="progress"></progress>
            <span x-text="progress"></span> %
        </div>
    </div>
</div>
