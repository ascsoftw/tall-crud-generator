<div>
    <div x-data="{ selected : @entangle('selected').defer}">
        <x-tall-crud-accordion-header tab="1">
            Customize Text
            <x-slot name="help">
                Customize the Text of Buttons, Links and Headings.
            </x-slot>
        </x-tall-crud-accordion-header>

        <x-tall-crud-accordion-wrapper ref="advancedTab1" tab="1">
            @foreach ($advancedSettings['text'] as $key => $text)
            <div class="mt-4">
                <x-tall-crud-label>
                    {{ $this->getAdvancedSettingLabel($key)}}
                </x-tall-crud-label>
                <x-tall-crud-input class="block mt-1 w-1/4" type="text"
                    wire:model.defer="advancedSettings.text.{{$key}}" />
            </div>
            @endforeach
        </x-tall-crud-accordion-wrapper>

        <x-tall-crud-accordion-header tab="2">
            Flash Messages
            <x-slot name="help">
                Enable / Disable Flash Messages & Customize their Text.
            </x-slot>
        </x-tall-crud-accordion-header>

        <x-tall-crud-accordion-wrapper ref="advancedTab2" tab="2">
            <x-tall-crud-label class="mt-2">
                Enable Flash Messages:
                <x-tall-crud-checkbox class="ml-2" wire:model.defer="flashMessages.enable" />
            </x-tall-crud-label>

            @foreach (['add', 'edit', 'delete'] as $key)
            <div class="mt-4">
                <x-tall-crud-label>{{ Str::title($key)}}:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/2"
                    wire:model.defer="flashMessages.text.{{$key}}" />
            </div>
            @endforeach
        </x-tall-crud-accordion-wrapper>

        <x-tall-crud-accordion-header tab="3">
            Table Settings
            <x-slot name="help">
                Customize the Properties of Table displaying the Listing
            </x-slot>
        </x-tall-crud-accordion-header>
        <x-tall-crud-accordion-wrapper ref="advancedTab3" tab="3">
            <x-tall-crud-label class="mt-2">
                Show Pagination Dropdown:
                <x-tall-crud-checkbox class="ml-2" wire:model.defer="advancedSettings.table_settings.showPaginationDropdown" />
            </x-tall-crud-label>
            <x-tall-crud-checkbox-wrapper class="mt-4">
                <x-tall-crud-label>Records Per Page: </x-tall-crud-label>
                <x-tall-crud-select class="block mt-1 w-1/6 ml-2"
                    wire:model="advancedSettings.table_settings.recordsPerPage">
                    @foreach ([10, 15, 20, 50] as $p)
                    <option value="{{$p}}">{{$p}}</option>
                    @endforeach
                </x-tall-crud-select>
            </x-tall-crud-checkbox-wrapper>
            <x-tall-crud-label class="mt-4">
                Allow User to Hide Column in Listing <span class="italic">(only works with Alpine v3):</span>
                <x-tall-crud-checkbox class="ml-2" wire:model.defer="advancedSettings.table_settings.showHideColumns" />
            </x-tall-crud-label>
            <x-tall-crud-label class="mt-4">
                Enable Bulk Actions
                <x-tall-crud-checkbox class="ml-2" wire:model.defer="advancedSettings.table_settings.bulkActions" />
            </x-tall-crud-label>
            @if($this->advancedSettings['table_settings']['bulkActions'])
            <x-tall-crud-checkbox-wrapper>
                <x-tall-crud-label>Column to Change on Bulk Action: </x-tall-crud-label>
                <x-tall-crud-select class="block mt-1 w-1/6 ml-2"
                    wire:model="advancedSettings.table_settings.bulkActionColumn">
                    <option value="">-Select Column-</option>
                    @if (Arr::exists($modelProps, 'columns'))
                    @foreach ($modelProps['columns'] as $column)
                    <option value="{{$column}}">{{$column}}</option>
                    @endforeach
                    @endif
                </x-tall-crud-select>
            </x-tall-crud-checkbox-wrapper>
            @endif
            <div class="mt-4">The Table uses Blue Theme. You can change the theme by changing <span class="font-bold text-blue-700">blue</span> classes to other class. Check <a href="https://v2.tailwindcss.com/docs/customizing-colors" target="_blank" class="text-blue-300 cursor-pointer">v2</a> or <a class="text-blue-300 cursor-pointer" target="_blank" href="https://tailwindcss.com/docs/customizing-colors">v3</a> for other classes.</div>
            <div class="mt-4">
                <x-tall-crud-label>Class on th:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.th" />
            </div>
            <div class="mt-4">
                <x-tall-crud-label>Hover Class on tr:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trHover" />
            </div>
            <div class="mt-4">
                <x-tall-crud-label>Even Row Class:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trEven" />
            </div>
            <div class="mt-4">
                <x-tall-crud-label>Table Row Divide Class:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.trBottomBorder" />
            </div>
            <div class="mt-4">
                <x-tall-crud-label>Class on td:</x-tall-crud-label>
                <x-tall-crud-input type="text" class="mt-1 block w-1/4"
                    wire:model.defer="advancedSettings.table_settings.classes.td" />
            </div>
        </x-tall-crud-accordion-wrapper>
        <x-tall-crud-accordion-header tab="4">
            Filters
            <x-slot name="help">
                Define Filters so that Users can Search throuth the data from your Listing
            </x-slot>
        </x-tall-crud-accordion-header>

        <x-tall-crud-accordion-wrapper ref="advancedTab4" tab="4">
            <x-tall-crud-button class="mt-4" wire:click="createNewFilter">Add
            </x-tall-crud-button>
            <x-tall-crud-table class="mt-4">
                <x-slot name="header">
                    <x-tall-crud-table-column>Type</x-tall-crud-table-column>
                    <x-tall-crud-table-column>Column</x-tall-crud-table-column>
                    <x-tall-crud-table-column>Actions</x-tall-crud-table-column>
                </x-slot>
                @foreach ($this->filters as $i => $v)
                <tr>
                    <x-tall-crud-table-column>{{$v['type']}}</x-tall-crud-table-column>
                    <x-tall-crud-table-column>
                         @if($v['type'] == 'None' || $v['type'] == 'Date' )
                            @if($v['type'] == 'Date')
                                {{ $v['operator'] }} 
                            @endif
                            {{$v['column']}}
                         @elseif($v['type'] == 'BelongsTo' || $v['type'] == 'BelongsToMany')
                            {{$v['relation']. '.' . $v['column']}}
                         @endif
                    </x-tall-crud-table-column>
                    <x-tall-crud-table-column>
                        <x-tall-crud-button wire:click.prevent="deleteFilter({{$i}})" mode="delete">
                            Delete
                        </x-tall-crud-button>
                    </x-tall-crud-table-column>
                </tr>
                @endforeach
            </x-tall-crud-table>
        </x-tall-crud-accordion-wrapper>
    </div>
</div>

<x-tall-crud-dialog-modal wire:model="confirmingFilter">
    <x-slot name="title">
        Add a New Filter
    </x-slot>

    <x-slot name="content">
        <div class="mt-4">
            <div>
                <x-tall-crud-label>Select Type</x-tall-crud-label>
                <x-tall-crud-select class="block mt-1 w-1/2" wire:model.lazy="filter.type">
                    <option value="">-Please Select-</option>
                    <option value="None">None</option>
                    <option value="BelongsTo">Belongs To</option>
                    <option value="BelongsToMany">Belongs To Many</option>
                    <option value="Date">Date Filter</option>
                </x-tall-crud-select>
                @error('filter.type') <x-tall-crud-error-message>{{$message}}
                </x-tall-crud-error-message> @enderror
            </div>

            @if ($filter['isValid'])
            <div class="mt-4 p-4 rounded border border-gray-300">
                @if ( $filter['type'] == 'None' || $filter['type'] == 'Date')
                <div class="mt-4">
                    <x-tall-crud-label>
                        Column
                    </x-tall-crud-label>
                    <x-tall-crud-select class="block mt-1 w-1/2"
                        wire:model.lazy="filter.column">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($filter, 'columns'))
                        @foreach ($filter['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x-tall-crud-select>
                    @error('filter.column') <x-tall-crud-error-message>{{$message}}
                    </x-tall-crud-error-message> @enderror
                </div>
                @endif

                @if ( $filter['type'] == 'Date')
                <div class="mt-4">
                    <x-tall-crud-label>Label</x-tall-crud-label>
                    <x-tall-crud-input class="block mt-1 w-1/2" type="text" wire:model.defer="filter.label" />
                </div>
                <div class="mt-4">
                    <x-tall-crud-label>Operator</x-tall-crud-label>
                    <x-tall-crud-select class="block mt-1 w-1/6" wire:model.lazy="filter.operator">
                        <option value=">=">>=</option>
                        <option value=">">></option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                    </x-tall-crud-select>
                </div>
                @endif

                @if ( $filter['type'] == 'None')
                <div class="mt-4">
                    <x-tall-crud-label>Select Options (add as JSON)</x-tall-crud-label>
                    <x-tall-crud-input class="block mt-1 w-full" type="text" wire:model.defer="filter.options" />
                </div>
                @endif

                @if ( $filter['type'] == 'BelongsTo' || $filter['type'] == 'BelongsToMany')
                <div class="mt-4">
                    <x-tall-crud-label>
                        Relationship
                    </x-tall-crud-label>
                    <x-tall-crud-select class="block mt-1 w-1/2"
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
                    </x-tall-crud-select>
                    @error('filter.relation') <x-tall-crud-error-message>{{$message}}
                    </x-tall-crud-error-message> @enderror
                </div>
                @if ( !empty($filter['relation']))
                <div class="mt-4">
                    <x-tall-crud-label>
                        Column
                    </x-tall-crud-label>
                    <x-tall-crud-select class="block mt-1 w-1/2"
                        wire:model.lazy="filter.column">
                        <option value="">-Please Select-</option>
                        @if (Arr::exists($filter, 'columns'))
                        @foreach ($filter['columns'] as $c)
                        <option value="{{$c}}">{{$c}}</option>
                        @endforeach
                        @endif
                    </x-tall-crud-select>
                    @error('filter.column') <x-tall-crud-error-message>{{$message}}
                    </x-tall-crud-error-message> @enderror
                </div>
                @endif
                <x-tall-crud-label class="mt-4">
                    Filter Multiple Values
                    <x-tall-crud-checkbox class="ml-2" wire:model.defer="filter.isMultiple" />
                </x-tall-crud-label>
                @endif
            </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-tall-crud-button wire:click="$set('confirmingFilter', false)">Cancel</x-tall-crud-button>
        <x-tall-crud-button mode="add" wire:click="addFilter()">Save</x-tall-crud-button>
    </x-slot>
</x-tall-crud-dialog-modal>