<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        <x:tall-crud-generator::accordion-header tab="1">Customize Text</x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">
            <div>
                <x:tall-crud-generator::label>Heading of Listing Page</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.title" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Add Link Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.addLink" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Edit Link Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.editLink" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Delete Link Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.deleteLink" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Create Submit Buttton Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.createButton" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Edit Submit Buttton Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.editButton" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Delete Submit Buttton Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.deleteButton" />
            </div>

            <div class="mt-4">
                <x:tall-crud-generator::label>Cancel Buttton Text</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="advancedSettings.text.cancelButton" />
            </div>
        </x:tall-crud-generator::accordion-wrapper>

        <x:tall-crud-generator::accordion-header tab="2">
            Flash Messages
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab2" tab="2">
            <x:tall-crud-generator::checkbox-wrapper>
                <x:tall-crud-generator::label>Enable Flash Messages:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="flashMessages.enable" />
            </x:tall-crud-generator::checkbox-wrapper>

            <div class="mt-4">
                <x:tall-crud-generator::label>Add:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/2" wire:model.defer="flashMessages.text.add" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Edit:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/2" wire:model.defer="flashMessages.text.edit" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Delete:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/2" wire:model.defer="flashMessages.text.delete" />
            </div>
        </x:tall-crud-generator::accordion-wrapper>

        <x:tall-crud-generator::accordion-header tab="3">
            Table Settings
        </x:tall-crud-generator::accordion-header>
        <x:tall-crud-generator::accordion-wrapper ref="advancedTab3" tab="3">
            <x:tall-crud-generator::checkbox-wrapper>
                <x:tall-crud-generator::label>Show Pagination Dropdown:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="advancedSettings.table_settings.showPaginationDropdown" />
            </x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>Records Per Page</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/6" wire:model="advancedSettings.table_settings.recordsPerPage">
                    @foreach([10, 15, 20, 50] as $p)
                        <option value="{{$p}}">{{$p}}</option>
                    @endforeach
                </x:tall-crud-generator::select>
            </x:tall-crud-generator::checkbox-wrapper>
        </x:tall-crud-generator::accordion-wrapper>
    </div>
</div>