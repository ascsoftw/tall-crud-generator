<div>
    <div x-data="{ selected: null}">
        @if($componentProps['create_add_modal'] || $componentProps['create_edit_modal'])
        <x:tall-crud-generator::accordion-header @click="selected !== 1 ? selected = 1 : selected = null">
            Belongs To Many
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper x-ref="advancedTab1" x-bind:style="selected == 1 ? 'max-height: ' + $refs.advancedTab1.scrollHeight + 'px' : ''">
            <div>
                <x:tall-crud-generator::label>Enter Relation Name as defined in Model</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="belongsToManyRelation.name" />
                @error('belongsToManyRelation.name') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>

            <x:tall-crud-generator::button class="mt-4" wire:click="validateBelongsToManyRelation">Check</x:tall-crud-generator::button>

            @if($belongsToManyRelation['is_valid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if($componentProps['create_add_modal'])
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Add Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToManyRelation.in_add" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                @if($componentProps['create_edit_modal'])
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Edit Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToManyRelation.in_edit" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/4" wire:model.defer="belongsToManyRelation.displayColumn">
                        @foreach($belongsToManyRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                </div>
                <x:tall-crud-generator::button class="mt-4" wire:click="addBelongsToManyRelation">Add</x:tall-crud-generator::button>
            </div>
            @endif

            <x:tall-crud-generator::table class="mt-4">
                <x-slot name="header">
                    <x:tall-crud-generator::table-column>Relation</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Display Field</x:tall-crud-generator::table-column>
                    @if($componentProps['create_add_modal'])
                    <x:tall-crud-generator::table-column>In Add</x:tall-crud-generator::table-column>
                    @endif
                    @if($componentProps['create_edit_modal'])
                    <x:tall-crud-generator::table-column>In Edit</x:tall-crud-generator::table-column>
                    @endif
                    <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
                </x-slot>
                @foreach( $this->belongsToManyRelations as $i => $v)
                <tr>
                    <x:tall-crud-generator::table-column>{{$v['relationName']}}</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>{{$v['displayColumn']}}</x:tall-crud-generator::table-column>
                    @if($componentProps['create_add_modal'])
                    <x:tall-crud-generator::table-column>{{$v['in_add'] ? 'Yes' : 'No'}}</x:tall-crud-generator::table-column>
                    @endif
                    @if($componentProps['create_edit_modal'])
                    <x:tall-crud-generator::table-column>{{$v['in_edit'] ? 'Yes' : 'No'}}</x:tall-crud-generator::table-column>
                    @endif
                    <x:tall-crud-generator::table-column>
                        <x:tall-crud-generator::button wire:click.prevent="deleteBelongsToManyRelation({{$i}})" mode="delete">
                            Delete
                        </x:tall-crud-generator::button>
                    </x:tall-crud-generator::table-column>
                </tr>
                @endforeach
            </x:tall-crud-generator::table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif
    </div>
</div>