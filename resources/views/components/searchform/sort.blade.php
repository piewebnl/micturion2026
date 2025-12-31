@props(['searchFormData'])

@if (isset($searchFormData['sort']))
    <x-forms.select wire:change="toggleSort" :wireModel="'filterValues.sort'" id="sort" name="sort" label="{{ __('Sort') }}"
        :options="$searchFormData['sort']" placeholder="" />
@endif

@if (isset($searchFormData['order_toggle_icon']))
    <button wire:click="toggleOrder" type="button" class="mb-3" name="order" id="order">
        @if ($searchFormData['order_toggle_icon'] == 'up')
            <x-icons.up />
        @else
            <x-icons.down />
        @endif
    </button>
@endif
