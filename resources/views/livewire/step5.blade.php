<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        <x:tall-crud-generator::accordion-header tab="1">
            Listing
            <x-slot name="help">
                Change the Order of Columns displayed in the Listing
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">
            <x:tall-crud-generator::sort-fields-table mode="listing"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>

        @if($this->addFeature)
        <x:tall-crud-generator::accordion-header tab="2">
            Add Fields
            <x-slot name="help">
                Change the Order of Fields displayed in the Add Form
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab2" tab="2">
            <x:tall-crud-generator::sort-fields-table mode="add"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif

        @if($this->editFeature)
        <x:tall-crud-generator::accordion-header tab="3">
            Edit Fields
            <x-slot name="help">
                Change the Order of Fields displayed in the Edit Form
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab3" tab="3">
            <x:tall-crud-generator::sort-fields-table mode="edit"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif
    </div>
</div>