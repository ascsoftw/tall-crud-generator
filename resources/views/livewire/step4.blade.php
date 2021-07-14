<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        @if($componentProps['create_add_modal'] || $componentProps['create_edit_modal'])
        <x:tall-crud-generator::accordion-header tab="1">
            Belongs To Many
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">

            <x:tall-crud-generator::button class="mt-4" wire:click="createNewBelongsToManyRelation">Add</x:tall-crud-generator::button>
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
            <x:tall-crud-generator::button class="mt-4" wire:click="createNewBelongsToRelation">Add</x:tall-crud-generator::button>
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

        <x:tall-crud-generator::accordion-header tab="3">
            Eager Loading
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab3" tab="3">
            <x:tall-crud-generator::button class="mt-4" wire:click="createNewWithRelation">Add</x:tall-crud-generator::button>
            <x:tall-crud-generator::table class="mt-4">
                <x-slot name="header">
                    <x:tall-crud-generator::table-column>Relation</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Display Column</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
                </x-slot>
                @foreach( $this->withRelations as $i => $v)
                <tr>
                    <x:tall-crud-generator::table-column>{{$v['relationName']}}</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>{{$v['displayColumn']}}</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>
                        <x:tall-crud-generator::button wire:click.prevent="deleteWithRelation({{$i}})" mode="delete">
                            Delete
                        </x:tall-crud-generator::button>
                    </x:tall-crud-generator::table-column>
                </tr>
                @endforeach
            </x:tall-crud-generator::table>

        </x:tall-crud-generator::accordion-wrapper>
    </div>
</div>


<x:tall-crud-generator::dialog-modal wire:model="confirmingBelongsToMany">
    <x-slot name="title">
        Add a Belongs to Many Relationship
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Select Relationship</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.lazy="belongsToManyRelation.name">
                    <option value="">-Please Select-</option>
                    @foreach($allRelations['belongsToMany'] as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                </x:tall-crud-generator::select>
                @error('belongsToManyRelation.name') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>

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
                    <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.defer="belongsToManyRelation.displayColumn">
                        <option value="">-Please Select-</option>
                        @foreach($belongsToManyRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                    @error('belongsToManyRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
                </div>
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingBelongsToMany', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addBelongsToManyRelation()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>

<x:tall-crud-generator::dialog-modal wire:model="confirmingBelongsTo">
    <x-slot name="title">
        Add a Belongs to Relationship
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Select Relationship</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.lazy="belongsToRelation.name">
                    <option value="">-Please Select-</option>
                    @foreach($allRelations['belongsTo'] as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                </x:tall-crud-generator::select>
                @error('belongsToRelation.name') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>

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
                        <option value="">-Please Select-</option>
                        @foreach($belongsToRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                    @error('belongsToRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
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
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingBelongsTo', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addBelongsToRelation()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>


<x:tall-crud-generator::dialog-modal wire:model="confirmingWith">
    <x-slot name="title">
        Eager Load a Relationship
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Select Relationship</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.lazy="withRelation.name">
                    <option value="">-Please Select-</option>
                    @foreach($allRelations as $allRelation)
                    @foreach($allRelation as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                    @endforeach
                </x:tall-crud-generator::select>
                @error('withRelation.name') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>

            @if($withRelation['is_valid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.defer="withRelation.displayColumn">
                        <option value="">-Please Select-</option>
                        @foreach($withRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                    @error('withRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
                </div>
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingWith', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addWithRelation()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>