<div>
    <div x-data="{ selected: null}">
        <x:tall-crud-generator::accordion-header @click="selected !== 1 ? selected = 1 : selected = null">
            Listing
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab1" x-bind:style="selected == 1 ? 'max-height: ' + $refs.advancedTab1.scrollHeight + 'px' : ''">
            <x:tall-crud-generator::sort-fields-table mode="listing"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>

        @if($componentProps['create_add_modal'])
        <x:tall-crud-generator::accordion-header @click="selected !== 2 ? selected = 2 : selected = null">
            Add Fields
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab2" x-bind:style="selected == 2 ? 'max-height: ' + $refs.advancedTab2.scrollHeight + 'px' : ''">
            <x:tall-crud-generator::sort-fields-table mode="add"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif

        @if($componentProps['create_edit_modal'])
        <x:tall-crud-generator::accordion-header @click="selected !== 3 ? selected = 3 : selected = null">
            Edit Fields
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab3" x-bind:style="selected == 3 ? 'max-height: ' + $refs.advancedTab3.scrollHeight + 'px' : ''">
            <x:tall-crud-generator::sort-fields-table mode="edit"></x:tall-crud-generator::sort-fields-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif
    </div>
</div>