<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        <x:tall-crud-generator::accordion-header tab="1">
            Customize Text
            <x-slot name="help">
                Customize the Text of Buttons, Links and Headings.
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">
            @foreach ($advancedSettings['text'] as $key => $text)
            <div class="mt-4">
                <x:tall-crud-generator::label>
                    {{ Str::title(
                        Str::replace(
                            '-', ' ', Str::kebab($key)
                        )) 
                    }}
                </x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text"
                    wire:model.defer="advancedSettings.text.{{$key}}" />
            </div>
            @endforeach
        </x:tall-crud-generator::accordion-wrapper>

        <x:tall-crud-generator::accordion-header tab="2">
            Flash Messages
            <x-slot name="help">
                Enable / Disable Flash Messages & Customize their Text.
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab2" tab="2">
            <x:tall-crud-generator::checkbox-wrapper>
                <x:tall-crud-generator::label>Enable Flash Messages:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="flashMessages.enable" />
            </x:tall-crud-generator::checkbox-wrapper>

            @foreach (['add', 'edit', 'delete'] as $key)
            <div class="mt-4">
                <x:tall-crud-generator::label>{{ Str::title($key)}}:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/2"
                    wire:model.defer="flashMessages.text.{{$key}}" />
            </div>
            @endforeach
        </x:tall-crud-generator::accordion-wrapper>

        <x:tall-crud-generator::accordion-header tab="3">
            Table Settings
            <x-slot name="help">
                Customize the Properties of Table displaying the Listing
            </x-slot>
        </x:tall-crud-generator::accordion-header>
        <x:tall-crud-generator::accordion-wrapper ref="advancedTab3" tab="3">
            <x:tall-crud-generator::checkbox-wrapper>
                <x:tall-crud-generator::label>Show Pagination Dropdown:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2"
                    wire:model.defer="advancedSettings.table_settings.showPaginationDropdown" />
            </x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>Records Per Page</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/6"
                    wire:model="advancedSettings.table_settings.recordsPerPage">
                    @foreach ([10, 15, 20, 50] as $p)
                    <option value="{{$p}}">{{$p}}</option>
                    @endforeach
                </x:tall-crud-generator::select>
            </x:tall-crud-generator::checkbox-wrapper>
            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>Allow User to Hide Column in Listing:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2"
                    wire:model.defer="advancedSettings.table_settings.showHideColumns" />
            </x:tall-crud-generator::checkbox-wrapper>
        </x:tall-crud-generator::accordion-wrapper>
    </div>
</div>