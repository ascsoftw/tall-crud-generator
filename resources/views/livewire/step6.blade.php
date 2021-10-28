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
                    {{ $this->getAdvancedSettingLabel($key)}}
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
                <x:tall-crud-generator::label>Records Per Page: </x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/6 ml-2"
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
            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>Enable Bulk Actions:</x:tall-crud-generator::label>
                <x:tall-crud-generator::checkbox class="ml-2"
                    wire:model="advancedSettings.table_settings.bulkActions" />
            </x:tall-crud-generator::checkbox-wrapper>
            @if($this->advancedSettings['table_settings']['bulkActions'])
            <x:tall-crud-generator::checkbox-wrapper>
                <x:tall-crud-generator::label>Column to Change on Bulk Action: </x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/6 ml-2"
                    wire:model="advancedSettings.table_settings.bulkActionColumn">
                    <option value="">-Select Column-</option>
                    @if (Arr::exists($modelProps, 'columns'))
                    @foreach ($modelProps['columns'] as $column)
                    <option value="{{$column}}">{{$column}}</option>
                    @endforeach
                    @endif
                </x:tall-crud-generator::select>
            </x:tall-crud-generator::checkbox-wrapper>
            @endif
            <div class="mt-4">
                <x:tall-crud-generator::label>Class on th:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.th" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Hover Class on tr:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trHover" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Even Row Class:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trEven" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Table Row Divide Class:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trBottomBorder" />
            </div>
            <div class="mt-4">
                <x:tall-crud-generator::label>Class on td:</x:tall-crud-generator::label>
                <x:tall-crud-generator::input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.td" />
            </div>
        </x:tall-crud-generator::accordion-wrapper>
        <x:tall-crud-generator::accordion-header tab="4">
            Filters
            <x-slot name="help">
                Define Filters for the Listing
            </x-slot>
        </x:tall-crud-generator::accordion-header>

        <x:tall-crud-generator::accordion-wrapper ref="advancedTab4" tab="4">
            <x:tall-crud-generator::button class="mt-4" wire:click="createNewFilter">Add
            </x:tall-crud-generator::button>
            <x:tall-crud-generator::table class="mt-4">
                <x-slot name="header">
                    <x:tall-crud-generator::table-column>Type</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Column</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>Actions</x:tall-crud-generator::table-column>
                </x-slot>
                @foreach ($this->filters as $i => $v)
                <tr>
                    <x:tall-crud-generator::table-column>{{$v['type']}}</x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>
                         @if($v['type'] == 'None')
                            {{$v['column']}}
                         @elseif($v['type'] == 'BelongsTo' || $v['type'] == 'BelongsToMany')
                            {{$v['relation']. '.' . $v['column']}}
                         @endif
                    </x:tall-crud-generator::table-column>
                    <x:tall-crud-generator::table-column>
                        <x:tall-crud-generator::button wire:click.prevent="deleteFilter({{$i}})" mode="delete">
                            Delete
                        </x:tall-crud-generator::button>
                    </x:tall-crud-generator::table-column>
                </tr>
                @endforeach
            </x:tall-crud-generator::table>
        </x:tall-crud-generator::accordion-wrapper>
    </div>
</div>

<x:tall-crud-generator::dialog-modal wire:model="confirmingFilter">
    <x-slot name="title">
        Add a New Filter
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x:tall-crud-generator::label>Select Type</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/2" wire:model.lazy="filter.type">
                    <option value="">-Please Select-</option>
                    <option value="None">None</option>
                    <option value="BelongsTo">Belongs To</option>
                    <option value="BelongsToMany">Belongs To Many</option>
                </x:tall-crud-generator::select>
                @error('filter.type') <x:tall-crud-generator::error-message>{{$message}}
                </x:tall-crud-generator::error-message> @enderror
            </div>

            @if ($filter['isValid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if ( $filter['type'] == 'None')
                <div class="mt-4">
                    <x:tall-crud-generator::label>
                        Column
                    </x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2"
                        wire:model.lazy="filter.column">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($filter, 'columns'))
                        @foreach ($filter['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('filter.column') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
                </div>

                <div class="mt-4">
                    <x:tall-crud-generator::label>Select Options (add as JSON)</x:tall-crud-generator::label>
                    <x:tall-crud-generator::input class="block mt-1 w-full" type="text" wire:model.defer="filter.options" />
                </div>
                @endif

                @if ( $filter['type'] == 'BelongsTo' || $filter['type'] == 'BelongsToMany')
                <div class="mt-4">
                    <x:tall-crud-generator::label>
                        Relationship
                    </x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2"
                        wire:model.lazy="filter.relation">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($allRelations, 'belongsTo') && $filter['type'] == 'BelongsTo')
                        @foreach ($allRelations['belongsTo'] as $c)
                        <option value="{{$c['name']}}">{{$c['name']}}</option>
                        @endforeach
                        @endif
                        @if (Arr::exists($allRelations, 'belongsToMany')  && $filter['type'] == 'BelongsToMany')
                        @foreach ($allRelations['belongsToMany'] as $c)
                        <option value="{{$c['name']}}">{{$c['name']}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('filter.relation') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
                </div>
                @if ( !empty($filter['relation']))
                <div class="mt-4">
                    <x:tall-crud-generator::label>
                        Column
                    </x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-1/2"
                        wire:model.lazy="filter.column">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($filter, 'columns'))
                        @foreach ($filter['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x:tall-crud-generator::select>
                    @error('filter.column') <x:tall-crud-generator::error-message>{{$message}}
                    </x:tall-crud-generator::error-message> @enderror
                </div>
                @endif
                <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                    <x:tall-crud-generator::label>Filter Multiple Values</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="filter.isMultiple" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endif
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x:tall-crud-generator::button wire:click="$set('confirmingFilter', false)">Cancel</x:tall-crud-generator::button>
        <x:tall-crud-generator::button mode="add" wire:click="addFilter()">Save</x:tall-crud-generator::button>
    </x-slot>
</x:tall-crud-generator::dialog-modal>