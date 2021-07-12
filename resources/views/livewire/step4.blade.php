<div>
    @json($belongsToRelation)
    <div x-data="{ selected : @entangle('selected').defer}">
        @if($componentProps['create_add_modal'] || $componentProps['create_edit_modal'])
        <x:tall-crud-generator::accordion-header tab="1">
            Belongs To Many
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">
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

        @if($componentProps['create_add_modal'] || $componentProps['create_edit_modal'])
        <x:tall-crud-generator::accordion-header tab="2">
            Belongs To 
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab2" tab="2">
            <div>
                <x:tall-crud-generator::label>Enter Relation Name as defined in Model</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/4" type="text" wire:model.defer="belongsToRelation.name" />
                @error('belongsToRelation.name') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>

            <x:tall-crud-generator::button class="mt-4" wire:click="validateBelongsToRelation">Check</x:tall-crud-generator::button>

            @if($belongsToRelation['is_valid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if($componentProps['create_add_modal'])
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Add Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToRelation.in_add" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                @if($componentProps['create_edit_modal'])
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Edit Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToRelation.in_edit" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/4" wire:model.defer="belongsToRelation.displayColumn">
                        @foreach($belongsToRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                </div>
                <div class="mt-4">
                    <x:tall-crud-generator::label>Map to Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/4" wire:model.defer="belongsToRelation.column">
                        <option value="">-Please Select-</option>
                        @foreach($modelProps['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                    @error('belongsToRelation.column') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
                </div>
                <x:tall-crud-generator::button class="mt-4" wire:click="addBelongsToRelation">Add</x:tall-crud-generator::button>
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
                @foreach( $this->belongsToRelations as $i => $v)
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
                        <x:tall-crud-generator::button wire:click.prevent="deleteBelongsToRelation({{$i}})" mode="delete">
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