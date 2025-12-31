@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-red-500']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
