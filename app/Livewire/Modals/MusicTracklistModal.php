<?php

namespace App\Livewire\Modals;

use App\Models\Music\Album;
use App\Models\Music\Song;
use App\Models\Music\SpineImage;
use App\Traits\QueryCache\QueryCache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class MusicTracklistModal extends ModalComponent
{
    use QueryCache;

    protected static array $maxWidths = [
        '2xl' => 'custom-modal-width',
    ];

    public string $albumId;

    #[On('music-tracklist-modal-save-cropped-spine-image')]
    public function saveCroppedSpineImage($id, $croppedTempFile)
    {

        $spineImage = SpineImage::find($id);

        $source = $croppedTempFile;

        $dest = config('music.spine_images_path') . '/' . $spineImage->slug . '.png';
        copy($source, $dest);

        $spineImage->checked = true;
        $spineImage->save();

        session()->flash('success', 'Image saved');
    }

    // Save the original and keep it in itunes
    public function saveSpineImage($spineImageId)
    {
        $album = Album::find($this->albumId);

        $spineImage = new SpineImage;
        $slug = $spineImage->getSpineImageSlug($album);

        $source = storage_path(config('music.spine_images_storage_path') . '/' . $slug . '.jpg');
        $destination = config('music.spine_images_path') . '/' . $slug . '.jpg';

        copy($source, $destination);

        $spineImage = SpineImage::find($spineImageId);
        $spineImage->checked = true;
        $spineImage->save();
        session()->flash('success', 'Image saved');
    }

    public function render()
    {

        $album = Album::with(['artist', 'spineImage', 'discogsReleases'])->where('id', $this->albumId)->first();

        $songs = Song::with('album.artist')->where('album_id', $this->albumId)
            ->orderBy('disc_number')
            ->orderBy('track_number')->get(['id', 'name', 'album_artist', 'track_number', 'disc_number', 'grouping', 'time', 'rating']);

        // Discogs back artwork from image cropper
        $sourceImageUrls = [];
        $discogsRelease = $album->discogsReleases->first();
        if (isset($discogsRelease['artwork_other_urls'])) {
            $images = json_decode($discogsRelease['artwork_other_urls']);
            if (is_array($images)) {
                foreach ($images as $index => $image) {
                    $sourceImageUrls[$index] = Storage::url(
                        'discogs-back-artwork/' .
                            $discogsRelease['release_id'] .
                            '-' .
                            $index +
                            1 .
                            '.jpg',
                    );
                }
            }
        }

        return view('livewire.modals.music-tracklist', compact('album', 'songs', 'discogsRelease', 'sourceImageUrls'));
    }
}
