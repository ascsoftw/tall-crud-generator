<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

class Template
{
    public static function getSortableHeader()
    {
        return <<<'EOT'
                    <div class="flex items-center">
                        <button wire:click="sortBy('##COLUMN##')">##LABEL##</button>
                        ##SORT_ICON_HTML##
                    </div>
EOT;
    }

    public static function getSearchInputField()
    {
        return <<<'EOT'

                <input wire:model.debounce.500ms="q" type="search" placeholder="Search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                <span class="ml-3 mt-2" wire:loading.delay wire:target="q">
                    <x:tall-crud-generator::loading-indicator />
                </span>
EOT;
    }

    public static function getHideColumnsDropdown()
    {
        return <<<'EOT'

                <x:tall-crud-generator::dropdown class="flex justify-items items-center mr-4 border border-rounded px-2 cursor-pointer">
                    <x-slot name="trigger">
                        Columns
                    </x-slot>

                    <x-slot name="content">
                        @foreach($columns as $c)
                        <x:tall-crud-generator::checkbox-wrapper class="mt-2">
                            <x:tall-crud-generator::checkbox wire:model="selectedColumns" value="{{ $c }}" /><x:tall-crud-generator::label class="ml-2">{{$c}}</x:tall-crud-generator::label>
                        </x:tall-crud-generator::checkbox-wrapper>
                        @endforeach
                    </x-slot>
                </x:tall-crud-generator::dropdown>
EOT;
    }

    public static function getPaginationSelectElement()
    {
        return <<<'EOT'

                <x:tall-crud-generator::select class="block w-1/10" wire:model="per_page">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </x:tall-crud-generator::select>
EOT;
    }

    public static function getDeleteButton()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showDeleteForm', {{ $result->##PRIMARY_KEY##}});" class="text-red-500">
                            <x:tall-crud-generator::icon-delete />
                        </button>
EOT;
    }

    public static function getAddButton()
    {
        return <<<'EOT'

        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showCreateForm');" class="text-blue-500">
            <x:tall-crud-generator::icon-add />
        </button>
EOT;
    }

    public static function getEditButton()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showEditForm', {{ $result->##PRIMARY_KEY##}});" class="text-green-500">
                            <x:tall-crud-generator::icon-edit />
                        </button>
EOT;
    }

    public static function getDeleteFeatureCode()
    {
        return <<<'EOT'


    public function showDeleteForm(int $id): void
    {
        $this->confirmingItemDeletion = true;
        $this->primaryKey = $id;
    }

    public function deleteItem(): void
    {
        ##MODEL##::destroy($this->primaryKey);
        $this->confirmingItemDeletion = false;
        $this->primaryKey = '';
        $this->reset(['item']);
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    public static function getAddFeatureCode()
    {
        return <<<'EOT'
 
    public function showCreateForm(): void
    {
        $this->confirmingItemCreation = true;
        $this->resetErrorBag();
        $this->reset(['item']);##BTM_INIT####BELONGS_TO_INIT##
    }

    public function createItem(): void
    {
        $this->validate();
        $item = ##MODEL##::create([##CREATE_FIELDS####BELONGS_TO_SAVE##
        ]);##BTM_ATTACH##
        $this->confirmingItemCreation = false;
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    public static function getAddFieldTemplate()
    {
        return <<<'EOT'
'##COLUMN##' => $this->item['##COLUMN##'] ?? ##DEFAULT_VALUE##, 
EOT;
    }

    public static function getEditFeatureCode()
    {
        return <<<'EOT'
 
    public function showEditForm(##MODEL## $##MODEL_VAR##): void
    {
        $this->resetErrorBag();
        $this->item = $##MODEL_VAR##;
        $this->confirmingItemEdit = true;##BTM_FETCH####BELONGS_TO_INIT##
    }

    public function editItem(): void
    {
        $this->validate();
        $this->item->save();##BTM_UPDATE##
        $this->confirmingItemEdit = false;
        $this->primaryKey = '';
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    public static function getListenerArray()
    {
        return <<<'EOT'
    /**
     * @var array
     */
    protected $listeners = [
        '##DELETE_LISTENER##',
        '##ADD_LISTENER##',
        '##EDIT_LISTENER##',
    ];

EOT;
    }

    public static function getSortingMethod()
    {
        return <<<'EOT'


    public function sortBy(string $field): void
    {
        if ($field == $this->sortBy) {
            $this->sortAsc = !$this->sortAsc;
        }
        $this->sortBy = $field;
    }
EOT;
    }

    public static function getSearchMethod()
    {
        return <<<'EOT'


    public function updatingQ(): void
    {
        $this->resetPage();
    }
EOT;
    }

    public static function getPaginationDropdownMethod()
    {
        return <<<'EOT'


    public function updatingPerPage(): void
    {
        $this->resetPage();
    }
EOT;
    }

    public static function getTableColumnSlot()
    {
        return <<<'EOT'
{{ $result->##COLUMN_NAME##}}
EOT;
    }

    public static function getSortingVariables()
    {
        return <<<'EOT'

    /**
     * @var string
     */
    public $sortBy = '##SORT_COLUMN##';

    /**
     * @var bool
     */
    public $sortAsc = true;

EOT;
    }

    public static function getSortingQuery()
    {
        return <<<'EOT'

            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
EOT;
    }

    public static function getDeleteVariables()
    {
        return <<<'EOT'
    /**
     * @var bool
     */
    public $confirmingItemDeletion = false;

    /**
     * @var string | int
     */
    public $primaryKey;

EOT;
    }

    public static function getAddVariables()
    {
        return <<<'EOT'

    /**
     * @var bool
     */
    public $confirmingItemCreation = false;

EOT;
    }

    public static function getEditVariables()
    {
        return <<<'EOT'

    /**
     * @var bool
     */
    public $confirmingItemEdit = false;
EOT;
    }

    public static function getSearchVariables()
    {
        return <<<'EOT'

    /**
     * @var string
     */
    public $q;

EOT;
    }

    public static function getPaginationVariables()
    {
        return <<<'EOT'

    /**
     * @var int
     */
    public $per_page = ##PER_PAGE##;

EOT;
    }

    public static function getSearchQueryCode()
    {
        return <<<'EOT'

            ->when($this->q, function ($query) {
                return $query->where(function ($query) {
##WHERE_CLAUSE##;
                });
            })
EOT;
    }

    public static function getSearchQueryWhereClause()
    {
        return <<<'EOT'
##QUERY##('##COLUMN##', 'like', '%' . $this->q . '%')
EOT;
    }

//     public static function getChildItemTemplate()
//     {
//         return <<<'EOT'

//     public $item;
// EOT;
//     }

    public static function getRulesArray()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    protected $rules = [##RULES##
    ];

EOT;
    }

    public static function getKeyValueTemplate()
    {
        return <<<'EOT'
'item.##COLUMN_NAME##' => '##VALUE##',
EOT;
    }

    public static function getValidationAttributesArray()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    protected $validationAttributes = [##ATTRIBUTES##
    ];


EOT;
    }

    public static function getDeleteModal()
    {
        return <<<'EOT'

    <x:tall-crud-generator::confirmation-dialog wire:model="confirmingItemDeletion">
        <x-slot name="title">
            Delete Record
        </x-slot>

        <x-slot name="content">
            Are you sure you want to Delete Record?
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemDeletion', false)">##CANCEL_BTN_TEXT##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="delete" wire:loading.attr="disabled" wire:click="deleteItem()">##DELETE_BTN_TEXT##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::confirmation-dialog>

EOT;
    }

    public static function getAddModal()
    {
        return <<<'EOT'

    <x:tall-crud-generator::dialog-modal wire:model="confirmingItemCreation">
        <x-slot name="title">
            Add Record
        </x-slot>

        <x-slot name="content">##FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemCreation', false)">##CANCEL_BTN_TEXT##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="add" wire:loading.attr="disabled" wire:click="createItem()">##CREATE_BTN_TEXT##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::dialog-modal>

EOT;
    }

    public static function getEditModal()
    {
        return <<<'EOT'

    <x:tall-crud-generator::dialog-modal wire:model="confirmingItemEdit">
        <x-slot name="title">
            Edit Record
        </x-slot>

        <x-slot name="content">##FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemEdit', false)">##CANCEL_BTN_TEXT##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="add" wire:loading.attr="disabled" wire:click="editItem()">##EDIT_BTN_TEXT##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::dialog-modal>
EOT;
    }

    public static function getInputField()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x:tall-crud-generator::label>##LABEL##</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/2" type="text" wire:model.defer="item.##COLUMN##" />
                @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>
EOT;
    }

    public static function getSelectField()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x:tall-crud-generator::label>##LABEL##</x:tall-crud-generator::label>
                <x:tall-crud-generator::select class="block mt-1 w-1/4" wire:model.defer="item.##COLUMN##">##OPTIONS##
                </x:tall-crud-generator::select> 
                @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>
EOT;
    }

    public static function getCheckboxField()
    {
        return <<<'EOT'

            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>##LABEL##:</x:tall-crud-generator::label><x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="item.##COLUMN##" />
            </x:tall-crud-generator::checkbox-wrapper>
            @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
EOT;
    }

    public static function getFlashCode()
    {
        return <<<'EOT'

        $this->emitTo('livewire-toast', 'show', '##MESSAGE##');
EOT;
    }

    public static function getEmptyArray()
    {
        return <<<'EOT'
    /**
     * @var ##TYPE##
     */
    public $##NAME## = [];

EOT;
    }

    public static function getSelfFilterInitCode()
    {
        return <<<'EOT'
        '##KEY##' => [
                'label' => '##LABEL##',
                'options' => [##OPTIONS##
                ]
            ],
EOT;
    }

    public static function getBtmInitCode()
    {
        return <<<'EOT'


        $this->##RELATION## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();
        $this->##FIELD_NAME## = [];
EOT;
    }

    public static function getBtmAttachCode()
    {
        return <<<'EOT'

        $item->##RELATION##()->attach($this->##FIELD_NAME##);
EOT;
    }

    public static function getBtmFetchCode()
    {
        return <<<'EOT'

        $this->##FIELD_NAME## = $##MODEL_VAR##->##RELATION##->pluck("##KEY##")->map(function ($i) {
            return (string)$i;
        })->toArray();
        $this->##RELATION## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();

EOT;
    }

    public static function getBtmUpdateCode()
    {
        return <<<'EOT'

        $this->item->##RELATION##()->sync($this->##FIELD_NAME##);
        $this->##FIELD_NAME## = [];
EOT;
    }

    public static function getUseModelCode()
    {
        return <<<'EOT'

use ##MODEL##;
EOT;
    }

    public static function getBtmFieldTemplate()
    {
        return <<<'EOT'


            <h2 class="mt-4">##HEADING##</h2>
            <div class="grid grid-cols-3">
                @foreach( $##RELATION## as $c)
                <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                    <x:tall-crud-generator::label>{{$c->##DISPLAY_COLUMN##}}</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox value="{{ $c->##RELATED_KEY## }}" class="ml-2" wire:model.defer="##FIELD_NAME##" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endforeach
            </div>
EOT;
    }

    public static function getBtmFieldMultiSelectTemplate()
    {
        return <<<'EOT'


            <h2 class="mt-4">##HEADING##</h2>
            <x:tall-crud-generator::select multiple="multiple" wire:model.defer="##FIELD_NAME##">
            @foreach( $##RELATION## as $c)
                <option value="{{ $c->##RELATED_KEY## }}">{{$c->##DISPLAY_COLUMN##}}</option>
            @endforeach
            </x:tall-crud-generator::select>
EOT;
    }

    public static function getBelongsToFieldTemplate()
    {
        return <<<'EOT'


            <div class="grid grid-cols-3">
                <div class="mt-4">
                    <x:tall-crud-generator::label>##LABEL##</x:tall-crud-generator::label>
                    <x:tall-crud-generator::select class="block mt-1 w-full" wire:model.defer="item.##FOREIGN_KEY##">
                        <option value="">Please Select</option>
                        @foreach($##BELONGS_TO_VAR## as $c)
                        <option value="{{$c->##OWNER_KEY##}}">{{$c->##DISPLAY_COLUMN##}}</option>
                        @endforeach
                    </x:tall-crud-generator::select>
                    @error('item.##FOREIGN_KEY##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
                </div>
            </div>
EOT;
    }

    public static function getBelongsToInitCode()
    {
        return <<<'EOT'

        $this->##BELONGS_TO_VAR## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();
EOT;
    }

    public static function getWithQueryCode()
    {
        return <<<'EOT'

            ->with([##RELATIONS##])
EOT;
    }

    public static function getWithCountQueryCode()
    {
        return <<<'EOT'

            ->withCount([##RELATIONS##])
EOT;
    }

    public static function getBtmTableSlot()
    {
        return <<<'EOT'
##RELATION##->implode('##DISPLAY_COLUMN##', ',')
EOT;
    }

    public static function getBelongsToTableSlot()
    {
        return <<<'EOT'
##RELATION##?->##DISPLAY_COLUMN##
EOT;
    }

    public static function getFlashComponent()
    {
        return <<<'EOT'

    @livewire('livewire-toast')
EOT;
    }

    public static function getAllColumns()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    public $columns = [##COLUMNS##];

EOT;
    }

    public static function getHideColumnInitCode()
    {
        return <<<'EOT'
        $this->selectedColumns = $this->columns;
EOT;
    }

    public static function getHideColumnMethod()
    {
        return <<<'EOT'


    public function showColumn($column)
    {
        return in_array($column, $this->selectedColumns);
    }
EOT;
    }

    public static function getHideColumnIfCondition()
    {
        return <<<'EOT'
@if($this->showColumn('##LABEL##'))
EOT;
    }

    public static function getBulkActionMethod()
    {
        return <<<'EOT'


    public function changeStatus(string $status): void
    {
        if (!empty($this->selectedItems)) {
            ##MODEL##::whereIn('##PRIMARY_KEY##', $this->selectedItems)->update(['##COLUMN##' => $status]);
            $this->selectedItems = [];
            $this->emitTo('livewire-toast', 'show', 'Records Updated Successfully.');
        } else {
            $this->emitTo('livewire-toast', 'showWarning', 'Please select some Records.');
        }
    }
EOT;
    }

    public static function getBulkActionDropdown()
    {
        return <<<'EOT'
                <x:tall-crud-generator::dropdown class="flex justify-items items-center mr-4 border border-rounded px-2 cursor-pointer">
                    <x-slot name="trigger">
                        Bulk Actions
                    </x-slot>

                    <x-slot name="content">
                        <button wire:click="changeStatus(1)">Activate</button><br />
                        <button wire:click="changeStatus(0)">Deactivate</button>
                    </x-slot>
                </x:tall-crud-generator::dropdown>
EOT;
    }

    public static function getBulkColumnCheckbox()
    {
        return <<<'EOT'

                        <x:tall-crud-generator::checkbox class="mr-2 leading-tight" value="{{$result->##PRIMARY_KEY##}}" wire:model.defer="selectedItems" />

EOT;
    }

    public static function getFilterInitTemplate()
    {
        return <<<'EOT'

        $this->filters = [##FILTERS##];
EOT;
    }

    public static function getRelationFilterInitTemplate()
    {
        return <<<'EOT'


        $##VAR## = ##MODEL##::pluck('##COLUMN##', '##OWNER_KEY##')->map(function($i, $k) {
            return ['key' => $k, 'label' => $i];
        })->toArray();
        $this->filters['##FOREIGN_KEY##']['label'] = '##LABEL##';
        $this->filters['##FOREIGN_KEY##']['options'] = ['0' => ['key' => '', 'label' => 'Any']] + $##VAR##;
EOT;
    }

    public static function getKeyLabelTemplate()
    {
        return <<<'EOT'
['key' => '##KEY##', 'label' => '##LABEL##'],
EOT;
    }

    public static function getFilterDropdownTemplate()
    {
        return <<<'EOT'

                <x:tall-crud-generator::dropdown class="flex justify-items items-center border border-rounded ml-4 px-4 cursor-pointer" width="w-72">
                    <x-slot name="trigger">
                        <span class="flex">
                        Filters <x:tall-crud-generator::icon-filter />
                        </span>
                    </x-slot>
                
                    <x-slot name="content">
                        @foreach($filters as $f => $filter)
                        <div class="mt-4">
                            <x:tall-crud-generator::label class="font-sm font-bold">
                                {{ $filter['label'] }}
                            </x:tall-crud-generator::label>
                            <x:tall-crud-generator::select class="w-3/4" wire:model="selectedFilters.{{$f}}">
                                @foreach($filter['options'] as $o)
                                <option value="{{$o['key']}}">{{$o['label']}}</option>
                                @endforeach
                            </x:tall-crud-generator::select>
                        </div>
                        @endforeach
                        <div class="my-4">
                            <x:tall-crud-generator::button wire:click="resetFilters()">Reset</x:tall-crud-generator::button>
                        </div>
                    </x-slot>
                </x:tall-crud-generator::dropdown>
EOT;
    }

    public static function getFilterMethodTemplate()
    {
        return <<<'EOT'


    public function updatingSelectedFilters(): void
    {
        $this->resetPage();
    }

    public function isFilterSet(string $column): bool
    {
        if( isset($this->selectedFilters[$column]) && $this->selectedFilters[$column] != '') {
            return true;
        }
        return false;
    }

    public function resetFilters(): void
    {
        $this->reset('selectedFilters');
    }
EOT;
    }

    public static function getFilterQueryTemplate()
    {
        return <<<'EOT'
            ->when($this->isFilterSet('##COLUMN##'), function($query) {
                return $query->where('##COLUMN##', $this->selectedFilters['##COLUMN##']);
            })
EOT;
    }

    public static function getFilterQueryBtmTemplate()
    {
        return <<<'EOT'
            ->when($this->isFilterSet('##COLUMN##'), function($query) {
                return $query->whereHas('##RELATION##', function($query) {
                    return $query->where('##TABLE##.##RELATED_KEY##', $this->selectedFilters['##COLUMN##']);
                });
            })
EOT;
    }
}
