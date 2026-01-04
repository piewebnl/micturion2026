<div class="mb-6">
    <form x-data="albumLookup(@js($searchFormData['albums']))" x-cloak class="max-w-full" @submit.prevent>
        <div class="relative max-w-xl">
            <x-forms.input id="album" name="album" type="text" label="Album"
                placeholder="Start typing an album or artist"
                :inputAttributes="[
                    'x-model' => 'query',
                    '@focus' => 'openList()',
                    '@input.debounce.200ms' => 'onInput()',
                    '@keydown.escape.prevent' => 'closeList()',
                    '@keydown.arrow-down.prevent' => 'focusNext()',
                    '@keydown.arrow-up.prevent' => 'focusPrev()',
                    '@keydown.enter.prevent' => 'selectFocused()',
                ]" />
            <div x-show="open && filtered().length" @click.outside="closeList()"
                class="absolute left-0 top-full z-40 mt-2 max-h-64 w-full overflow-y-auto rounded border border-zinc-300 bg-white shadow dark:border-zinc-700 dark:bg-zinc-900">
                <template x-for="(album, index) in filtered()" :key="album.value">
                    <button type="button" @click="select(album)" :class="optionClass(index)"
                        class="block w-full px-3 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <span x-text="album.label"></span>
                    </button>
                </template>
            </div>
        </div>
    </form>
</div>

<script>
    function albumLookup(options) {
        return {
            options: options ?? [],
            query: '',
            open: false,
            focusedIndex: 0,
            selected: null,
            openList() {
                this.open = true;
            },
            closeList() {
                this.open = false;
                this.focusedIndex = 0;
            },
            onInput() {
                this.open = true;
                if (this.selected && this.query !== this.selected.label) {
                    this.selected = null;
                    this.$wire.set('filterValues.album', null);
                    if (this.query.trim() === '') {
                        this.$wire.search();
                    }
                }
            },
            filtered() {
                const needle = this.query.toLowerCase().trim();
                const list = needle.length ?
                    this.options.filter(option => option.label.toLowerCase().includes(needle)) :
                    this.options;
                return list.slice(0, 12);
            },
            focusNext() {
                if (!this.open) {
                    this.open = true;
                }
                if (this.focusedIndex < this.filtered().length - 1) {
                    this.focusedIndex += 1;
                }
            },
            focusPrev() {
                if (this.focusedIndex > 0) {
                    this.focusedIndex -= 1;
                }
            },
            selectFocused() {
                const list = this.filtered();
                if (!list.length) {
                    return;
                }
                this.select(list[this.focusedIndex] ?? list[0]);
            },
            select(album) {
                this.selected = album;
                this.query = album.label;
                this.$wire.set('filterValues.album', album.value);
                this.$wire.search();
                this.closeList();
            },
            optionClass(index) {
                return {
                    'bg-zinc-100 dark:bg-zinc-800': this.focusedIndex === index,
                };
            },
        };
    }
</script>
