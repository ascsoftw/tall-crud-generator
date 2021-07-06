@props(['mode'])

@php
$count = count($this->sortFields[$mode])
@endphp

<x:tall-crud-generator::table class="mt-4">
    <x-slot name="header">
        <x:tall-crud-generator::table-column>Field</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Move Up</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Move Down</x:tall-crud-generator::table-column>
    </x-slot>
    @foreach( $this->sortFields[$mode] as $sortField)
    <tr>
        <x:tall-crud-generator::table-column>{{ $sortField['field'] }}</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>
            @if($sortField['order'] != 1)
            <a href="#" wire:click.prevent="moveUp('{{ $sortField['field'] }}', '{{$mode}}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </a>
            @endif
        </x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>
            @if($sortField['order'] != $count)
            <a href="#" wire:click.prevent="moveDown('{{ $sortField['field'] }}', '{{$mode}}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
            @endif
        </x:tall-crud-generator::table-column>
    </tr>
    @endforeach
</x:tall-crud-generator::table>