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

<x:tall-crud-generator::button class="mt-4" wire:click="{{$createMethod}}">Add
</x:tall-crud-generator::button>
<x:tall-crud-generator::table class="mt-4">
    <x-slot name="header">
        <x:tall-crud-generator::table-column>Relation</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Display Field</x:tall-crud-generator::table-column>
        @if ($this->addFeature)
        <x:tall-crud-generator::table-column>In Add</x:tall-crud-generator::table-column>
        @endif
        @if ($this->editFeature)
        <x:tall-crud-generator::table-column>In Edit</x:tall-crud-generator::table-column>
        @endif
        <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
    </x-slot>
    @foreach ($this->{$type} as $i => $v)
    <tr>
        <x:tall-crud-generator::table-column>{{$v['relationName']}}</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>{{$v['displayColumn']}}</x:tall-crud-generator::table-column>
        @if ($this->addFeature)
        <x:tall-crud-generator::table-column>{{$v['inAdd'] ? 'Yes' : 'No'}}
        </x:tall-crud-generator::table-column>
        @endif
        @if ($this->editFeature)
        <x:tall-crud-generator::table-column>{{$v['inEdit'] ? 'Yes' : 'No'}}
        </x:tall-crud-generator::table-column>
        @endif
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::button wire:click.prevent="{{$deleteMethod}}({{$i}})"
                mode="delete">
                Delete
            </x:tall-crud-generator::button>
        </x:tall-crud-generator::table-column>
    </tr>
    @endforeach
</x:tall-crud-generator::table>