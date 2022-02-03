<x-tall-crud-dropdown class="flex justify-items items-center border border-rounded ml-4 px-4 cursor-pointer" width="w-72">
    <x-slot name="trigger">
        <span class="flex">
        Filters <x-tall-crud-icon-filter />
        </span>
    </x-slot>

    <x-slot name="content">
        @foreach($filters as $f => $filter)
        <div class="mt-4">
            <x-tall-crud-label class="font-sm font-bold">
                {{ $filter['label'] }}
            </x-tall-crud-label>
            @if(isset($filter['type']) && $filter['type'] == 'date')
                <x-tall-crud-input class="block mt-1 w-full" type="date" wire:model="selectedFilters.{{$f}}" />
            @elseif(isset($filter['multiple']) && $filter['multiple'])
                @foreach($filter['options'] as $o)
                <x-tall-crud-checkbox-wrapper class="mt-2">
                    <x-tall-crud-checkbox wire:model="selectedFilters.{{$f}}" value="{{ $o['key'] }}" />
                    <x-tall-crud-label class="ml-2">{{$o['label']}}</x-tall-crud-label>
                </x-tall-crud-checkbox-wrapper>
                @endforeach
            @else 
                <x-tall-crud-select class="w-3/4" wire:model="selectedFilters.{{$f}}">
                    @foreach($filter['options'] as $o)
                    <option value="{{$o['key']}}">{{$o['label']}}</option>
                    @endforeach
                </x-tall-crud-select>
            @endif
        </div>
        @endforeach
        <div class="my-4">
            <x-tall-crud-button wire:click="resetFilters()">Reset</x-tall-crud-button>
        </div>
    </x-slot>
</x-tall-crud-dropdown>