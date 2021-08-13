<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        <x:tall-crud-generator::accordion-header tab="1">
            Eager Load
            <x-slot name="help">
                Eager Load a Related Model to display in Listing
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab1" tab="1">
            <x:tall-crud-generator::button class="mt-4" wire:click="createNewWithRelation">Add
            </x:tall-crud-generator::button>
            <x:tall-crud-generator::table class="mt-4">
                <x-slot name="header">
                    <x:tall-crud-generator::table-column>Relation</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Display Column</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
                </x-slot>
                @foreach ($this->withRelations as $i => $v)
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

        <x:tall-crud-generator::accordion-header tab="2">
            Eager Load Count
            <x-slot name="help">
                Eager Load Count of a Related Model to display in Listing
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab2" tab="2">
            <x:tall-crud-generator::button class="mt-4" wire:click="createNewWithCountRelation">Add
            </x:tall-crud-generator::button>
            <x:tall-crud-generator::table class="mt-4">
                <x-slot name="header">
                    <x:tall-crud-generator::table-column>Relation</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Sortable</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
                </x-slot>
                @foreach ($this->withCountRelations as $i => $v)
                <tr>
                    <x:tall-crud-generator::table-column>{{$v['relationName']}}</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>{{$v['isSortable'] ? 'Yes' : 'No'}}
                    </x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>
                        <x:tall-crud-generator::button wire:click.prevent="deleteWitCounthRelation({{$i}})"
                            mode="delete">
                            Delete
                        </x:tall-crud-generator::button>
                    </x:tall-crud-generator::table-column>
                </tr>
                @endforeach
            </x:tall-crud-generator::table>
        </x:tall-crud-generator::accordion-wrapper>

        @if ($this->addFeature || $this->editFeature)
        <x:tall-crud-generator::accordion-header tab="3">
            Belongs To Many
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab3" tab="3">
            <x:tall-crud-generator::show-relations-table type="belongsToManyRelations"></x:tall-crud-generator::show-relations-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif

        @if ($this->addFeature || $this->editFeature)
        <x:tall-crud-generator::accordion-header tab="4">
            Belongs To
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab4" tab="4">
            <x:tall-crud-generator::show-relations-table type="belongsToRelations"></x:tall-crud-generator::show-relations-table>
        </x:tall-crud-generator::accordion-wrapper>
        @endif
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
                    @if (Arr::exists($allRelations, 'belongsToMany'))
                    @foreach ($allRelations['belongsToMany'] as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                    @endif
                </x:tall-crud-generator::select>
                @error('belongsToManyRelation.name') <x:tall-crud-generator::error-message>{{$message}}
                </x:tall-crud-generator::error-message> @enderror
            </div>

            @if ($belongsToManyRelation['isValid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if ($this->addFeature)
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Add Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToManyRelation.inAdd" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                @if ($this->editFeature)
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Edit Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToManyRelation.inEdit" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2"
                        wire:model.defer="belongsToManyRelation.displayColumn">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($belongsToManyRelation, 'columns'))
                        @foreach ($belongsToManyRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('belongsToManyRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
                </div>
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingBelongsToMany', false)">Cancel
        </x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addBelongsToManyRelation()">Save
        </x:tall-crud-generator::button>
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
                    @if (Arr::exists($allRelations, 'belongsTo'))
                    @foreach ($allRelations['belongsTo'] as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                    @endif
                </x:tall-crud-generator::select>
                @error('belongsToRelation.name') <x:tall-crud-generator::error-message>{{$message}}
                </x:tall-crud-generator::error-message> @enderror
            </div>

            @if ($belongsToRelation['isValid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if ($this->addFeature)
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Add Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToRelation.inAdd" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                @if ($this->editFeature)
                <x:tall-crud-generator::checkbox-wrapper>
                    <x:tall-crud-generator::label>Show in Edit Form:</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="belongsToRelation.inEdit" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/4"
                        wire:model.defer="belongsToRelation.displayColumn">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($belongsToRelation, 'columns'))
                        @foreach ($belongsToRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('belongsToRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
                </div>
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingBelongsTo', false)">Cancel
        </x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addBelongsToRelation()">Save
        </x:tall-crud-generator::button>
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
                    @foreach ($allRelations as $allRelation)
                    @foreach ($allRelation as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                    @endforeach
                </x:tall-crud-generator::select>
                @error('withRelation.name') <x:tall-crud-generator::error-message>{{$message}}
                </x:tall-crud-generator::error-message> @enderror
            </div>

            @if ($withRelation['isValid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                <div class="mt-4">
                    <x:tall-crud-generator::label>Display Column</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2"
                        wire:model.defer="withRelation.displayColumn">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($withRelation, 'columns'))
                        @foreach ($withRelation['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('withRelation.displayColumn') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
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

<x:tall-crud-generator::dialog-modal wire:model="confirmingWithCount">
    <x-slot name="title">
        Eager Load Count
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Select Relationship</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.lazy="withCountRelation.name">
                    <option value="">-Please Select-</option>
                    @foreach ($allRelations as $allRelation)
                    @foreach ($allRelation as $c)
                    <option value="{{$c['name']}}">{{$c['name']}}</option>
                    @endforeach
                    @endforeach
                </x:tall-crud-generator::select>
                @error('withCountRelation.name') <x:tall-crud-generator::error-message>{{$message}}
                </x:tall-crud-generator::error-message> @enderror
            </div>

            @if ($withCountRelation['isValid'])
            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>Make Heading Sortable</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="withCountRelation.isSortable" />
            </x:tall-crud-generator::checkbox-wrapper>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingWithCount', false)">Cancel
        </x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addWithCountRelation()">Save
        </x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>