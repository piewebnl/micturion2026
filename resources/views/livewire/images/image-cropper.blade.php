<div x-data="cropperComponent(@js($sourceImageUrl))" class="my-4" wire:ignore>

    <div class="flex flex-row">
        <div>
            <template x-if="preview">
                <div class="pb-4 pr-4">
                    <span class="mb-4 text-gray-500">Preview</span>
                    <img :src="preview" class="max-w-full border">
                </div>

            </template>

        </div>
        <div>

            <div class="mb-4 flex flex-row flex-wrap items-end gap-4">
                <div class="inline-block border">
                    <canvas x-ref="zoomCanvas"></canvas>
                </div>

                <div>
                    <button type="button" class="btn btn-primary" @click="$wire.cropped = preview; $wire.save();">
                        Save Cropped Image
                    </button>
                    <button type="button" class="btn" @click="crop()">
                        Preview crop
                    </button>
                    <button type="button" class="btn" @click="reset()">
                        Reset
                    </button>
                </div>
            </div>


            <canvas x-ref="canvas" @mousedown.prevent="start($event)" @mousemove.window="move($event)"
                @mouseup.window="end()"></canvas>

            @error('cropped')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror

            @if ($saved)
                <div class="mt-4 space-y-2">
                    <p class="text-sm text-green-500">Cropped image saved!</p>
                </div>
            @endif
        </div>
    </div>
</div>
