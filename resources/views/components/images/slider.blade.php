@props(['images' => [], 'type', 'showArrows' => true])


@if ($images && count($images) > 1)
    <main class="mb-6 flex" x-data="carouselFilter()" {{ $attributes->merge(['class' => '']) }} wire:ignore>
        <div class="container grid">
            <div class="col-start-1 row-start-2" x-show="active == 0" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90">
                <div class="grid grid-cols-1 grid-rows-1" x-data="carousel()" x-init="init()">
                    <div class="carousel col-start-1 row-start-1" x-ref="carousel">
                        @foreach ($images as $key => $image)
                            <x-images.image type="{{ $type }}" alt="tekst" size="500"
                                label="{{ $image['label'] }}" slug="{{ $image['slug'] }}"
                                largestWidth="{{ $image['largest_width'] }}"
                                {{ $attributes->merge(['class' => 'absolute']) }} hash="{{ $image['hash'] }}"
                                :enlarge=true />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <script src="https://unpkg.com/flickity@2.3.0/dist/flickity.pkgd.min.js"></script>

    <script>
        function carousel() {
            return {
                active: 0,
                init() {
                    var flkty = new Flickity(this.$refs.carousel, {
                        wrapAround: true,
                        prevNextButtons: false,
                    });
                    flkty.on('change', i => this.active = i);
                }
            }
        }

        function carouselFilter() {
            return {
                active: 0,
                changeActive(i) {
                    this.active = i;

                    this.$nextTick(() => {
                        let flkty = Flickity.data(this.$el.querySelectorAll('.carousel')[i]);
                        flkty.resize();
                    });
                }
            }
        }
    </script>
@else
    @foreach ($images as $key => $image)
        <x-images.image type="{{ $type }}" alt="tekst" label="{{ $image['label'] }}" size="500"
            slug="{{ $image['slug'] }}" largestWidth="{{ $image['largest_width'] }}"
            {{ $attributes->merge(['class' => 'absolute']) }} hash="{{ $image['hash'] }}" :enlarge=true />
    @endforeach
@endif
