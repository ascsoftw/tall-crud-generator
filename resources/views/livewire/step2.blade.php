<div>
    <div x-data="{showPrimaryKeyInListing : @entangle('primaryKeyProps.inList').defer }">
        <div class="text-black bg-gray-200 p-4">Table Name is: <span class="font-bold">
                {{$modelProps['tableName']}}</span></div>
        <div class="text-black bg-gray-200 p-4 mt-2">Primary Key is: <span class="font-bold">
                {{$modelProps['primaryKey']}}</span></div>

        <x-tall-crud-h2>Primary Key Features</x-tall-crud-h2>
        <x-tall-crud-label class="mt-2">
            Display In Listing:
            <x-tall-crud-checkbox class="ml-2" wire:model.defer="primaryKeyProps.inList"
                @click="showPrimaryKeyInListing = ! showPrimaryKeyInListing " />
        </x-tall-crud-label>

        <x-tall-crud-label class="mt-2" x-show="showPrimaryKeyInListing">
            Sortable:
            <x-tall-crud-checkbox class="ml-2" wire:model.defer="primaryKeyProps.sortable" />
        </x-tall-crud-label>

        <div x-show="showPrimaryKeyInListing">
            <x-tall-crud-label class="mt-2">Label:</x-tall-crud-label>
            <x-tall-crud-input type="text" class="mt-1 block w-1/4" wire:model.defer="primaryKeyProps.label"
                placeholder="Label" />
        </div>

        <x-tall-crud-h2>Select Modal</x-tall-crud-h2>

        <x-tall-crud-label class="mt-2">
            Add Modal
            <x-tall-crud-checkbox class="ml-2" wire:model.defer="componentProps.createAddModal" />
        </x-tall-crud-label>

        <x-tall-crud-label class="mt-2">
            Edit Modal
            <x-tall-crud-checkbox class="ml-2" wire:model.defer="componentProps.createEditModal" />
        </x-tall-crud-label>

        <x-tall-crud-label class="mt-2">
            Delete Modal
            <x-tall-crud-checkbox class="ml-2" wire:model.defer="componentProps.createDeleteButton" />
        </x-tall-crud-label>
    </div>
</div>