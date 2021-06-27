<div x-data="{ showAdvanced: @entangle('showAdvanced').defer,
    showPrimaryKeyInListing : @entangle('primaryKeyProps.in_list').defer
}">
    <div>
        <x:tall-crud-generator::label>Full Path to Your Model (e.g. App\Models\Product)</x:tall-crud-generator::label>
        <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="modelPath" required disabled="{{$isValidModel}}" />
        @error('modelPath') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
    </div>

    @if(!$isValidModel)
    <x:tall-crud-generator::button wire:click="checkModel" class="mt-4">Check Model</x:tall-crud-generator::button>
    @endif

    @if($isValidModel)
    <div class="mt-4">
        <div class="text-black">Table Name is: <span class="font-bold"> {{$modelProps['table_name']}}</span></div>
        <div class="text-black">Primary Key is: <span class="font-bold"> {{$modelProps['primary_key']}}</span></div>

        <x:tall-crud-generator::h2>Primary Key Features</x:tall-crud-generator::h2>
        <x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::label>Display In Listing:</x:tall-crud-generator::label>
            <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="primaryKeyProps.in_list" @click="showPrimaryKeyInListing = ! showPrimaryKeyInListing " />
        </x:tall-crud-generator::checkbox-wrapper>

        <x:tall-crud-generator::checkbox-wrapper x-show="showPrimaryKeyInListing">
            <x:tall-crud-generator::label>Sortable:</x:tall-crud-generator::label>
            <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="primaryKeyProps.sortable" />
        </x:tall-crud-generator::checkbox-wrapper>

        <div x-show="showPrimaryKeyInListing">
            <x:tall-crud-generator::label>Label:</x:tall-crud-generator::label>
            <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4" wire:model.defer="primaryKeyProps.label" placeholder="Label" />
        </div>

        <x:tall-crud-generator::h2>Select Features</x:tall-crud-generator::h2>
        <x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::label>Add Modal:</x:tall-crud-generator::label>
            <x:tall-crud-generator::checkbox class="ml-2" wire:model="componentProps.create_add_modal" />
        </x:tall-crud-generator::checkbox-wrapper>

        <x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::label>Edit Modal:</x:tall-crud-generator::label>
            <x:tall-crud-generator::checkbox class="ml-2" wire:model="componentProps.create_edit_modal" />
        </x:tall-crud-generator::checkbox-wrapper>

        <x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::label>Delete Button:</x:tall-crud-generator::label>
            <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="componentProps.create_delete_button" />
        </x:tall-crud-generator::checkbox-wrapper>

        <x:tall-crud-generator::h2>Select Fields</x:tall-crud-generator::h2>
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
                    <x:tall-crud-generator::button wire:click.prevent="showAttributes({{$i}})" mode="edit" class="mr-8 mt-4">
                        Attributes
                    </x:tall-crud-generator::button>
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

        <div class="mt-4">
            <x:tall-crud-generator::label>Name of your Livewire Component</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="componentName" required />
            @error('componentName') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message>@enderror
        </div>

        <div class="cursor-pointer text-blue-500 font-medium mt-4" @click="showAdvanced = !showAdvanced" />
        Advanced Section
    </div>

    <div x-show="showAdvanced" class="mt-2 py-2 border-t-2 border-b-2">
        <div>
            <x:tall-crud-generator::label>Heading of Listing Page</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.title" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Add Link Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.add_link" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Edit Link Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.edit_link" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Delete Link Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.delete_link" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Create Submit Buttton Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.create_button" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Edit Submit Buttton Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.edit_button" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Delete Submit Buttton Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.delete_button" />
        </div>

        <div class="mt-4">
            <x:tall-crud-generator::label>Cancel Buttton Text</x:tall-crud-generator::label>
            <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.cancel_button" />
        </div>
    </div>

    @if($isComplete)
    <div class="flex items-center justify-end">
        @if($exitCode == 0)
        <div>
            <div class="text-green-500 font-bold italic">
                Files Generated Successfully! <br />
                Use the Following code to render Livewire Component.
            </div>
            <div class="bg-black text-white text-2xl mt-2 p-4 rounded-md">{{$generatedCode}}</div>

        </div>
        @else
        <x:tall-crud-generator::error-message>Files Could not be Generated.</x:tall-crud-generator::error-message>
        @endif
    </div>
    @endif
    <div class="flex items-center justify-end">
        <x:tall-crud-generator::button wire:click.prevent="validateSettings" class="bg-orange-500 hover:bg-orange-800 mt-4">Generate Files</x:tall-crud-generator::button>
    </div>
</div>
@endif

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
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingAttributes', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="setAttributes()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>

</div>