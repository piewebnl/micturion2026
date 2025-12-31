<div x-data="tiermaker()" x-init="init()" class="mx-auto max-w-7xl p-4">

    <div class="flex flex-col gap-1">
        @foreach ($labels as $i => $label)
            <div class="flex flex-row gap-1 bg-zinc-800">
                <div
                    class="text-gray-800 pointer-events-none w-[100px] h-[100px] flex items-center justify-center bg-tier-{{ strtolower($label) }}">
                    {{ $label }}</div>
                <div class="flex w-full flex-row items-center" x-ref="col-{{ $label }}">
                    @foreach ($tiers[$label] as $key => $id)
                        @php
                            $item = $albumsById[$id];
                        @endphp
                        <div class="z-10 shrink-0" data-id="{{ $item['persistent_id'] }}">
                            <x-images.image type="album" slug="{{ $item['album_image_slug'] }}" size="100"
                                largestWidth="{{ $item['album_image_largest_width'] }}"
                                hash="{{ $item['album_image_hash'] }}" class="cursor-pointer" />

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-2 flex flex-wrap gap-1 overflow-x-auto" x-ref="col-pool">
        @foreach ($tiers['pool'] as $id)
            @php $item = $albumsById[$id]; @endphp
            <div class="z-10 shrink-0" data-id="{{ $item['persistent_id'] }}">
                <x-images.image type="album" slug="{{ $item['album_image_slug'] }}" size="75"
                    largestWidth="{{ $item['album_image_largest_width'] }}" hash="{{ $item['album_image_hash'] }}"
                    class="cursor-pointer" />
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tiermaker', () => ({
            sortables: {},
            init() {
                // Include Pool + all labels
                const cols = ['pool', ...@json($labels)];

                cols.forEach((label) => {
                    const el = this.$refs['col-' + label];
                    if (!el) return;

                    this.sortables[label] = new Sortable(el, {
                        group: {
                            name: 'tiers',
                            pull: true,
                            put: true
                        },
                        // Make sure sorting within a tier is on
                        sort: true,
                        //animation: 150,
                        ghostClass: 'sortable-ghost',
                        draggable: '[data-id]',
                        direction: 'horizontal',
                        //emptyInsertThreshold: 30,
                        //swapThreshold: 0.1,
                        forceFallback: true,
                        fallbackOnBody: true,
                        //fallbackTolerance: 3,

                        // Fire once per drag operation (covers add/remove/reorder)
                        onEnd: () => this.pushState(),
                    });
                });
            },
            collect() {
                const payload = {};
                const seen = new Set();

                Object.keys(this.sortables).forEach((label) => {
                    const el = this.$refs['col-' + label];
                    if (!el) return;

                    payload[label] = Array.from(el.querySelectorAll('[data-id]'))
                        .map(c => c.getAttribute('data-id'))
                        .filter(id => id && (seen.has(id) ? false : (seen.add(id), true)));
                });

                return payload;
            },
            pushState() {
                const payload = this.collect();
                this.$wire.updateTiers(payload);
            },
        }));
    });
</script>
