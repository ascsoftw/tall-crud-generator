@props(['placeholder' => 'Search'])

<input wire:model.debounce.500ms="q" type="search" placeholder="{{$placeholder}}"
    {{ $attributes->merge(['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']) }} />
<span class="ml-3 mt-2" wire:loading.delay wire:target="q">
    <x:tall-crud-generator::loading-indicator />
</span>
