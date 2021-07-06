<div>
    <div x-data="{ selected: null}">
        <x:tall-crud-generator::accordion-header @click="selected !== 1 ? selected = 1 : selected = null">Customize Text</x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab1" x-bind:style="selected == 1 ? 'max-height: ' + $refs.advancedTab1.scrollHeight + 'px' : ''">
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
        </x:tall-crud-generator::accordion-wrapper>

        <x:tall-crud-generator::accordion-header @click="selected !== 2 ? selected = 2 : selected = null">
            Flash Messages
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab2" x-bind:style="selected == 2 ? 'max-height: ' + $refs.advancedTab2.scrollHeight + 'px' : ''">
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
    </div>
</div>