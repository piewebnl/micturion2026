<?php

namespace App\Livewire\Images;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ImageCropper extends Component
{
    public string $sourceImageUrl;

    // public string $sourceImage;

    // public string $destinationImage;

    public string $handler;

    public int $id;

    public int $index;

    public bool $saved = false;

    public ?string $cropped = null;

    public function mount(): void
    {
        // $this->sourceImage = $sourceImage;
        // $this->destinationImage = $destinationImage;
    }

    public function save(): void
    {
        if (!$this->cropped) {
            $this->addError('cropped', 'No cropped data received');

            return;
        }

        [$meta, $content] = explode(',', $this->cropped, 2);
        $binary = base64_decode($content);

        // Bepaal extensie uit meta
        $extension = match (true) {
            str_contains($meta, 'image/jpeg') => 'jpg',
            str_contains($meta, 'image/webp') => 'webp',
            str_contains($meta, 'image/png') => 'png',
            default => 'png',
        };

        $filename = uniqid('crop_', true) . '.' . $extension;

        $relativePath = 'tmp/image-cropper/' . $filename;
        Storage::disk('local')->put($relativePath, $binary);

        $croppedTempFile = Storage::disk('local')->path($relativePath);

        // Handlers
        if ($this->handler == 'saveSpineImage') {
            $this->dispatch('music-tracklist-modal-save-cropped-spine-image', $this->id, $croppedTempFile);
        }

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.images.image-cropper');
    }
}
