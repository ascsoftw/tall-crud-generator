@props(['tab' => 1])

<div class="mt-2 flex flex-start">
    <span {{$attributes->merge(['class' => 'cursor-pointer text-blue-500 font-medium']) }} wire:click="showHideAccordion({{$tab}})">{{$slot}}</span>
    @if(isset($help))
    <x-tall-crud-tooltip>
        {{$help}}
    </x-tall-crud-tooltip>
    @endif
</div>