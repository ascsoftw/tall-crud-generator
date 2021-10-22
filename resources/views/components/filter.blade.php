<x:tall-crud-generator::dropdown class="flex justify-items items-center border border-rounded ml-4 px-4 cursor-pointer" width="w-72">
    <x-slot name="trigger">
        <span class="flex">
        Filters <x:tall-crud-generator::icon-filter />
        </span>
    </x-slot>

    <x-slot name="content">
        @foreach($filters as $f => $filter)
        <div class="mt-4">
            <x:tall-crud-generator::label class="font-sm font-bold">
                {{ $filter['label'] }}
            </x:tall-crud-generator::label>
            <x:tall-crud-generator::select class="w-3/4" wire:model="selectedFilters.{{$f}}">
                @foreach($filter['options'] as $o)
                <option value="{{$o['key']}}">{{$o['label']}}</option>
                @endforeach
            </x:tall-crud-generator::select>
        </div>
        @endforeach
        <div class="my-4">
            <x:tall-crud-generator::button wire:click="resetFilters()">Reset</x:tall-crud-generator::button>
        </div>
    </x-slot>
</x:tall-crud-generator::dropdown>