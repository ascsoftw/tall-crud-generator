@props(['width' => '48'])

@php
switch ($width) {
    case '48':
        $width = 'w-48';
        break;
}
@endphp

<div {{ $attributes->merge(['class' => 'relative',]) }} x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-2 {{$width}} rounded-md shadow-lg origin-top-left top-0"
            style="display: none;"
        >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white w-full px-4">
            {{ $content }}
        </div>
    </div>
</div>
