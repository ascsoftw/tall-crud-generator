@props(['mode'])

@php
$classes = [
    'add' => 'bg-blue-500 hover:bg-blue-800',
    'edit' => 'bg-yellow-500 hover:bg-yellow-800',
    'delete' => 'bg-red-500 hover:bg-red-800',
    'default' => 'bg-gray-800 hover:bg-gray-700',
][$mode ?? 'default'];
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 '. $classes]) }}>
    {{ $slot }}
</button>
