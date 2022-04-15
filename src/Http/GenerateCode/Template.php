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
                <x-tall-crud-input-search />
EOT;
    }

    public static function getHideColumnsDropdown()
    {
        return <<<'EOT'

                <x-tall-crud-columns-dropdown />
EOT;
    }

    public static function getAlpineCode()
    {
        return <<<'EOT'
x-data="{
    selectedColumns: @entangle('selectedColumns').defer,
    columns: @entangle('columns').defer,
    hasColumn(column) {
        var columns = this.selectedColumns;
        var column = columns.find(e => {
            return e.toLowerCase() === column.toLowerCase()
        })
        return column != undefined;
    }
}"
EOT;
    }

    public static function getPaginationSelectElement()
    {
        return <<<'EOT'

                <x-tall-crud-page-dropdown />
EOT;
    }

    public static function getDeleteButton()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showDeleteForm', {{ $result->##PRIMARY_KEY##}});" class="text-red-500">
                            <x-tall-crud-icon-delete />
                        </button>
EOT;
    }

    public static function getAddButton()
    {
        return <<<'EOT'

        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showCreateForm');" class="text-blue-500">
            <x-tall-crud-icon-add />
        </button>
EOT;
    }

    public static function getEditButton()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showEditForm', {{ $result->##PRIMARY_KEY##}});" class="text-green-500">
                            <x-tall-crud-icon-edit />
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
{{ $result->##COLUMN_NAME## }}
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

    <x-tall-crud-confirmation-dialog wire:model="confirmingItemDeletion">
        <x-slot name="title">
            Delete Record
        </x-slot>

        <x-slot name="content">
            Are you sure you want to Delete Record?
        </x-slot>

        <x-slot name="footer">
            <x-tall-crud-button wire:click="$set('confirmingItemDeletion', false)">##CANCEL_BTN_TEXT##</x-tall-crud-button>
            <x-tall-crud-button mode="delete" wire:loading.attr="disabled" wire:click="deleteItem()">##DELETE_BTN_TEXT##</x-tall-crud-button>
        </x-slot>
    </x-tall-crud-confirmation-dialog>

EOT;
    }

    public static function getAddModal()
    {
        return <<<'EOT'

    <x-tall-crud-dialog-modal wire:model="confirmingItemCreation">
        <x-slot name="title">
            Add Record
        </x-slot>

        <x-slot name="content">##FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x-tall-crud-button wire:click="$set('confirmingItemCreation', false)">##CANCEL_BTN_TEXT##</x-tall-crud-button>
            <x-tall-crud-button mode="add" wire:loading.attr="disabled" wire:click="createItem()">##CREATE_BTN_TEXT##</x-tall-crud-button>
        </x-slot>
    </x-tall-crud-dialog-modal>

EOT;
    }

    public static function getEditModal()
    {
        return <<<'EOT'

    <x-tall-crud-dialog-modal wire:model="confirmingItemEdit">
        <x-slot name="title">
            Edit Record
        </x-slot>

        <x-slot name="content">##FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x-tall-crud-button wire:click="$set('confirmingItemEdit', false)">##CANCEL_BTN_TEXT##</x-tall-crud-button>
            <x-tall-crud-button mode="add" wire:loading.attr="disabled" wire:click="editItem()">##EDIT_BTN_TEXT##</x-tall-crud-button>
        </x-slot>
    </x-tall-crud-dialog-modal>
EOT;
    }

    public static function getInputField()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x-tall-crud-label>##LABEL##</x-tall-crud-label>
                <x-tall-crud-input class="block mt-1 w-1/2" type="text" wire:model.defer="item.##COLUMN##" />
                @error('item.##COLUMN##') <x-tall-crud-error-message>{{$message}}</x-tall-crud-error-message> @enderror
            </div>
EOT;
    }

    public static function getSelectField()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x-tall-crud-label>##LABEL##</x-tall-crud-label>
                <x-tall-crud-select class="block mt-1 w-1/4" wire:model.defer="item.##COLUMN##">##OPTIONS##
                </x-tall-crud-select> 
                @error('item.##COLUMN##') <x-tall-crud-error-message>{{$message}}</x-tall-crud-error-message> @enderror
            </div>
EOT;
    }

    public static function getCheckboxField()
    {
        return <<<'EOT'

            <x-tall-crud-checkbox-wrapper class="mt-4">
                <x-tall-crud-label>##LABEL##:</x-tall-crud-label><x-tall-crud-checkbox class="ml-2" wire:model.defer="item.##COLUMN##" />
            </x-tall-crud-checkbox-wrapper>
            @error('item.##COLUMN##') <x-tall-crud-error-message>{{$message}}</x-tall-crud-error-message> @enderror
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

    public static function getInitFilterCode()
    {
        return <<<'EOT'

    private function initFilters(): void
    {
##CODE##
        $this->initMultiFilters();
    }

EOT;
    }

    public static function getFilterMountCode()
    {
        return <<<'EOT'
        $this->initFilters();
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

    public static function getDateFilterInitCode()
    {
        return <<<'EOT'
        $this->filters['##COLUMN##']['label'] = '##LABEL##';
        $this->filters['##COLUMN##']['type'] = 'date';
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
                <x-tall-crud-checkbox-wrapper class="mt-4">
                    <x-tall-crud-label>{{$c->##DISPLAY_COLUMN##}}</x-tall-crud-label>
                    <x-tall-crud-checkbox value="{{ $c->##RELATED_KEY## }}" class="ml-2" wire:model.defer="##FIELD_NAME##" />
                </x-tall-crud-checkbox-wrapper>
                @endforeach
            </div>
EOT;
    }

    public static function getBtmFieldMultiSelectTemplate()
    {
        return <<<'EOT'


            <h2 class="mt-4">##HEADING##</h2>
            <x-tall-crud-select multiple="multiple" wire:model.defer="##FIELD_NAME##">
            @foreach( $##RELATION## as $c)
                <option value="{{ $c->##RELATED_KEY## }}">{{$c->##DISPLAY_COLUMN##}}</option>
            @endforeach
            </x-tall-crud-select>
EOT;
    }

    public static function getBelongsToFieldTemplate()
    {
        return <<<'EOT'


            <div class="grid grid-cols-3">
                <div class="mt-4">
                    <x-tall-crud-label>##LABEL##</x-tall-crud-label>
                    <x-tall-crud-select class="block mt-1 w-full" wire:model.defer="item.##FOREIGN_KEY##">
                        <option value="">Please Select</option>
                        @foreach($##BELONGS_TO_VAR## as $c)
                        <option value="{{$c->##OWNER_KEY##}}">{{$c->##DISPLAY_COLUMN##}}</option>
                        @endforeach
                    </x-tall-crud-select>
                    @error('item.##FOREIGN_KEY##') <x-tall-crud-error-message>{{$message}}</x-tall-crud-error-message> @enderror
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

    public static function getHideColumnIfCondition()
    {
        return <<<'EOT'
x-show="hasColumn('##LABEL##')"
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
                <x-tall-crud-dropdown class="flex justify-items items-center mr-4 border border-rounded px-2 cursor-pointer">
                    <x-slot name="trigger">
                        Bulk Actions
                    </x-slot>

                    <x-slot name="content">
                        <button wire:click="changeStatus(1)">Activate</button><br />
                        <button wire:click="changeStatus(0)">Deactivate</button>
                    </x-slot>
                </x-tall-crud-dropdown>
EOT;
    }

    public static function getBulkColumnCheckbox()
    {
        return <<<'EOT'

                        <x-tall-crud-checkbox class="mr-2 leading-tight" value="{{$result->##PRIMARY_KEY##}}" wire:model.defer="selectedItems" />

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
        ##IS_MULTIPLE##$this->filters['##FOREIGN_KEY##']['multiple'] = true;
        $this->filters['##FOREIGN_KEY##']['options'] = ##EMPTY_FILTER_KEY## $##VAR##;
EOT;
    }

    public static function getEmptyFilterKey()
    {
        return <<<'EOT'
['0' => ['key' => '', 'label' => 'Any']] +
EOT;
    }

    public static function getResetMultipleFilter()
    {
        return <<<'EOT'

        $this->selectedFilters['##FOREIGN_KEY##'] = [];
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

                <x-tall-crud-filter :filters=$filters />
EOT;
    }

    public static function getFilterMethodTemplate()
    {
        return <<<'EOT'


    public function updatingSelectedFilters(): void
    {
        $this->resetPage();
    }

    private function isFilterSet(string $column): bool
    {
        if (isset($this->selectedFilters[$column])) {
            if (is_array($this->selectedFilters[$column])) {
                if (!empty($this->selectedFilters[$column])) {
                    return true;
                }
            } else {
                if ($this->selectedFilters[$column] != '') {
                    return true;
                }
            }
        }
        return false;
    }

    public function resetFilters(): void
    {
        $this->reset('selectedFilters');
        $this->initMultiFilters();
    }

    private function initMultiFilters(): void
    {
##RESET_MULTI_FILTER##
    }
EOT;
    }

    public static function getFilterQueryTemplate()
    {
        return <<<'EOT'
            ->when($this->isFilterSet('##COLUMN##'), function($query) {
                return $query->##CLAUSE##('##COLUMN##', $this->selectedFilters['##COLUMN##']);
            })
EOT;
    }

    public static function getDateFilterQueryTemplate()
    {
        return <<<'EOT'
            ->when($this->isFilterSet('##LABEL##'), function($query) {
                return $query->##CLAUSE##('##COLUMN##', '##OPERATOR##', $this->selectedFilters['##LABEL##']);
            })
EOT;
    }

    public static function getFilterQueryBtmTemplate()
    {
        return <<<'EOT'
            ->when($this->isFilterSet('##COLUMN##'), function($query) {
                return $query->whereHas('##RELATION##', function($query) {
                    return $query->##CLAUSE##('##TABLE##.##RELATED_KEY##', $this->selectedFilters['##COLUMN##']);
                });
            })
EOT;
    }
}
