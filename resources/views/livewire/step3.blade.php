<x:tall-crud-generator::table class="mt-4">
    <x-slot name="header">
        <x:tall-crud-generator::table-column>Column</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Label</x:tall-crud-generator::table-column>
        @if(!( !$componentProps['create_add_modal'] && !$componentProps['create_edit_modal'] ))
        <x:tall-crud-generator::table-column>Display In Listing</x:tall-crud-generator::table-column>
        @endif
        @if($componentProps['create_add_modal'])
        <x:tall-crud-generator::table-column>Display In Create</x:tall-crud-generator::table-column>
        @endif
        @if($componentProps['create_edit_modal'])
        <x:tall-crud-generator::table-column>Display In Edit</x:tall-crud-generator::table-column>
        @endif
        <x:tall-crud-generator::table-column>Searchable</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Sortable</x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
    </x-slot>
    @foreach( $this->fields as $i => $field)
    <tr>
        <x:tall-crud-generator::table-column>
            <select wire:model.defer="fields.{{$i}}.column" class="form-select rounded-md shadow-sm">
                <option value="">-Select Column-</option>
                @foreach($this->modelProps['columns'] as $column)
                <option value="{{$column}}">{{$column}}</option>
                @endforeach
            </select>
        </x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::input type="text" class="mt-1 block w-full" wire:model.defer="fields.{{$i}}.label" placeholder="Label" />
        </x:tall-crud-generator::table-column>
        @if(!( !$componentProps['create_add_modal'] && !$componentProps['create_edit_modal'] ))
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::checkbox wire:model.defer="fields.{{$i}}.in_list" />
        </x:tall-crud-generator::table-column>
        @endif
        @if($componentProps['create_add_modal'])
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::checkbox wire:model.defer="fields.{{$i}}.in_add" />
        </x:tall-crud-generator::table-column>
        @endif
        @if($componentProps['create_edit_modal'])
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::checkbox wire:model.defer="fields.{{$i}}.in_edit" />
        </x:tall-crud-generator::table-column>
        @endif
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::checkbox wire:model.defer="fields.{{$i}}.searchable" />
        </x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>
            <x:tall-crud-generator::checkbox wire:model.defer="fields.{{$i}}.sortable" />
        </x:tall-crud-generator::table-column>
        <x:tall-crud-generator::table-column>
            @if(!( !$componentProps['create_add_modal'] && !$componentProps['create_edit_modal'] ))
            <x:tall-crud-generator::button wire:click.prevent="showAttributes({{$i}})" mode="edit" class="mr-8 mt-4">
                Attributes
            </x:tall-crud-generator::button>
            @endif
            <x:tall-crud-generator::button wire:click.prevent="deleteField({{$i}})" mode="delete" class="mr-8 mt-4">
                Delete
            </x:tall-crud-generator::button>
        </x:tall-crud-generator::table-column>
    </tr>
    @endforeach
</x:tall-crud-generator::table>


<div class="mt-4">
    <x:tall-crud-generator::button mode="add" wire:click.prevent="addField">
        Add New Fields
    </x:tall-crud-generator::button>
    @error('fields') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
</div>

<x:tall-crud-generator::dialog-modal wire:model="confirmingAttributes">
    <x-slot name="title">
        Attributes
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Enter Validations (Comma separated)
                    <x:tall-crud-generator::tag wire:click="clearRules()">Clear Options</x:tall-crud-generator::tag>
                </x:tall-crud-generator::label>
                <x:tall-crud-generator::input wire:model.defer="attributes.rules" class="block mt-1 w-full" type="text" />

                Popular Validations:
                <x:tall-crud-generator::tag wire:click="addRule('required')">Required</x:tall-crud-generator::tag>
                <x:tall-crud-generator::tag wire:click="addRule('min:3')">Min</x:tall-crud-generator::tag>
                <x:tall-crud-generator::tag wire:click="addRule('max:50')">Max</x:tall-crud-generator::tag>
                <x:tall-crud-generator::tag wire:click="addRule('numeric')">Numeric</x:tall-crud-generator::tag>
                <x:tall-crud-generator::tag wire:click="addRule('email')">Email</x:tall-crud-generator::tag>
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Field Type</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/4" wire:model="attributes.type">
                    <option value="input">Input</option>
                    <option value="select">Select</option>
                    <option value="checkbox">Checkbox</option>
                </x:tall-crud-generator::select>
            </div>

            @if($attributes['type'] == 'select')
            <x:tall-crud-generator::label>Select Options (add as JSON)</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-full" type="text" wire:model.defer="attributes.options" />
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingAttributes', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="setAttributes()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>
