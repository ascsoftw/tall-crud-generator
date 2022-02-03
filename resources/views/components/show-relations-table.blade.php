@props(['type'])

@switch($type)
    @case('belongsToManyRelations')
        @php
        $createMethod = 'createNewBelongsToManyRelation';
        $deleteMethod = 'deleteBelongsToManyRelation';
        @endphp
        @break
    @case('belongsToRelations')
        @php
        $createMethod = 'createNewBelongsToRelation';
        $deleteMethod = 'deleteBelongsToRelation';
        @endphp
        @break
    @default
@endswitch

<x-tall-crud-button class="mt-4" wire:click="{{$createMethod}}">Add
</x-tall-crud-button>
<x-tall-crud-table class="mt-4">
    <x-slot name="header">
        <x-tall-crud-table-column>Relation</x-tall-crud-table-column>
        <x-tall-crud-table-column>Display Field</x-tall-crud-table-column>
        @if ($this->addFeature)
        <x-tall-crud-table-column>In Add</x-tall-crud-table-column>
        @endif
        @if ($this->editFeature)
        <x-tall-crud-table-column>In Edit</x-tall-crud-table-column>
        @endif
        <x-tall-crud-table-column>Actions</x-tall-crud-table-column>
    </x-slot>
    @foreach ($this->{$type} as $i => $v)
    <tr>
        <x-tall-crud-table-column>{{$v['relationName']}}</x-tall-crud-table-column>
        <x-tall-crud-table-column>{{$v['displayColumn']}}</x-tall-crud-table-column>
        @if ($this->addFeature)
        <x-tall-crud-table-column>{{$v['inAdd'] ? 'Yes' : 'No'}}
        </x-tall-crud-table-column>
        @endif
        @if ($this->editFeature)
        <x-tall-crud-table-column>{{$v['inEdit'] ? 'Yes' : 'No'}}
        </x-tall-crud-table-column>
        @endif
        <x-tall-crud-table-column>
            <x-tall-crud-button wire:click.prevent="{{$deleteMethod}}({{$i}})"
                mode="delete">
                Delete
            </x-tall-crud-button>
        </x-tall-crud-table-column>
    </tr>
    @endforeach
</x-tall-crud-table>