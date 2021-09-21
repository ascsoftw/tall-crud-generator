<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

trait WithTemplates
{
    public function getSortingHeaderTemplate()
    {
        return <<<'EOT'
                    <div class="flex items-center">
                        <button wire:click="sortBy('##COLUMN##')">##LABEL##</button>
                        ##SORT_ICON_HTML##
                    </div>
EOT;
    }

    public static function getSearchBoxTemplate()
    {
        return <<<'EOT'

                <input wire:model.debounce.500ms="q" type="search" placeholder="Search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                <span class="ml-3 mt-2" wire:loading.delay wire:target="q">
                    <x:tall-crud-generator::loading-indicator />
                </span>
EOT;
    }

    public static function getHideColumnDropdownTemplate()
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

    public static function getPaginationDropdownTemplate()
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

    public static function getDeleteButtonTemplate()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showDeleteForm', {{ $result->##PRIMARY_KEY##}});" class="text-red-500">
                            <x:tall-crud-generator::icon-delete />
                        </button>
EOT;
    }

    public static function getAddButtonTemplate()
    {
        return <<<'EOT'

        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showCreateForm');" class="text-blue-500">
            <x:tall-crud-generator::icon-add />
        </button>
EOT;
    }

    public static function getEditButtonTemplate()
    {
        return <<<'EOT'

                        <button type="submit" wire:click="$emitTo('##COMPONENT_NAME##', 'showEditForm', {{ $result->##PRIMARY_KEY##}});" class="text-green-500">
                            <x:tall-crud-generator::icon-edit />
                        </button>
EOT;
    }

    public static function getDeleteMethodTemplate()
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

    public static function getAddMethodTemplate()
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

    public function getEditMethodTemplate()
    {
        return <<<'EOT'
 
    public function showEditForm(##MODEL## $##MODEL_VAR##): void
    {
        $this->resetErrorBag();
        $this->item = $##MODEL_VAR##;
        $this->confirmingItemEdition = true;##BTM_FETCH####BELONGS_TO_INIT##
    }

    public function editItem(): void
    {
        $this->validate();
        $this->item->save();##BTM_UPDATE##
        $this->confirmingItemEdition = false;
        $this->primaryKey = '';
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    public static function getChildListenerTemplate()
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

    public static function getSortingMethodTemplate()
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

    public static function getSearchMethodTemplate()
    {
        return <<<'EOT'


    public function updatingQ(): void
    {
        $this->resetPage();
    }
EOT;
    }

    public static function getPaginationDropdownMethodTemplate()
    {
        return <<<'EOT'


    public function updatingPerPage(): void
    {
        $this->resetPage();
    }
EOT;
    }

    public function getTableColumnTemplate()
    {
        return <<<'EOT'
{{ $result->##COLUMN_NAME##}}
EOT;
    }

    public static function getSortingVarsTemplate()
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

    public static function getSortingQueryTemplate()
    {
        return <<<'EOT'

            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
EOT;
    }

    public static function getDeleteVarsTemplate()
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

    public static function getAddVarsTemplate()
    {
        return <<<'EOT'

    /**
     * @var bool
     */
    public $confirmingItemCreation = false;

EOT;
    }

    public static function getEditVarsTemplate()
    {
        return <<<'EOT'

    /**
     * @var bool
     */
    public $confirmingItemEdition = false;
EOT;
    }

    public static function getSearchingVarsTemplate()
    {
        return <<<'EOT'

    /**
     * @var string
     */
    public $q;

EOT;
    }

    public static function getPaginationVarsTemplate()
    {
        return <<<'EOT'

    /**
     * @var int
     */
    public $per_page = ##PER_PAGE##;

EOT;
    }

    public static function getSearchQueryTemplate()
    {
        return <<<'EOT'

            ->when($this->q, function ($query) {
                return $query->where(function ($query) {
##WHERE_CLAUSE##;
                });
            })
EOT;
    }

    public static function getSearchQueryWhereTemplate()
    {
        return <<<'EOT'
##QUERY##('##COLUMN##', 'like', '%' . $this->q . '%')
EOT;
    }

    public static function getChildItemTemplate()
    {
        return <<<'EOT'

    public $item;
EOT;
    }

    public static function getChildRulesTemplate()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    protected $rules = [##RULES##
    ];

EOT;
    }

    public static function getChildFieldTemplate()
    {
        return <<<'EOT'
'item.##COLUMN_NAME##' => '##VALUE##',
EOT;
    }

    public function getChildValidationAttributesTemplate()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    protected $validationAttributes = [##ATTRIBUTES##
    ];


EOT;
    }

    public static function getDeleteModalTemplate()
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

    public static function getAddModalTemplate()
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

    public static function getEditModalTemplate()
    {
        return <<<'EOT'

    <x:tall-crud-generator::dialog-modal wire:model="confirmingItemEdition">
        <x-slot name="title">
            Edit Record
        </x-slot>

        <x-slot name="content">##FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemEdition', false)">##CANCEL_BTN_TEXT##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="add" wire:loading.attr="disabled" wire:click="editItem()">##EDIT_BTN_TEXT##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::dialog-modal>
EOT;
    }

    public function getInputFieldTemplate()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x:tall-crud-generator::label>##LABEL##</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/2" type="text" wire:model.defer="item.##COLUMN##" />
                @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>
EOT;
    }

    public function getSelectFieldTemplate()
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

    public function getCheckboxFieldTemplate()
    {
        return <<<'EOT'

            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>##LABEL##:</x:tall-crud-generator::label><x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="item.##COLUMN##" />
            </x:tall-crud-generator::checkbox-wrapper>
            @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
EOT;
    }

    public function getFieldTemplate($type = 'input')
    {
        switch ($type) {
            case 'checkbox':
                return $this->getCheckboxFieldTemplate();
            case 'select':
                return $this->getSelectFieldTemplate();
            case 'input':
            default:
                return $this->getInputFieldTemplate();
        }
    }

    public static function getFlashTemplate()
    {
        return <<<'EOT'

        $this->emitTo('livewire-toast', 'show', '##MESSAGE##');
EOT;
    }

    public static function getEmptyArrayTemplate()
    {
        return <<<'EOT'
    /**
     * @var ##TYPE##
     */
    public $##NAME## = [];

EOT;
    }

    public static function getSelfFilterInitTemplate()
    {
        return <<<'EOT'
        '##KEY##' => [
                'label' => '##LABEL##',
                'options' => [##OPTIONS##
                ]
            ],
EOT;
    }

    public static function getBtmInitTemplate()
    {
        return <<<'EOT'


        $this->##RELATION## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();
        $this->##FIELD_NAME## = [];
EOT;
    }

    public static function getBtmAttachTemplate()
    {
        return <<<'EOT'

        $item->##RELATION##()->attach($this->##FIELD_NAME##);
EOT;
    }

    public static function getBtmFetchTemplate()
    {
        return <<<'EOT'

        $this->##FIELD_NAME## = $##MODEL_VAR##->##RELATION##->pluck("##KEY##")->map(function ($i) {
            return (string)$i;
        })->toArray();
        $this->##RELATION## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();

EOT;
    }

    public static function getBtmUpdateTemplate()
    {
        return <<<'EOT'

        $this->item->##RELATION##()->sync($this->##FIELD_NAME##);
        $this->##FIELD_NAME## = [];
EOT;
    }

    //todo remove this.
    public function getOtherModelTemplate()
    {
        return <<<'EOT'

use ##MODEL##;
EOT;
    }

    public static function getUseModelTemplate()
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

    public static function getBelongsToInitTemplate()
    {
        return <<<'EOT'

        $this->##BELONGS_TO_VAR## = ##MODEL##::orderBy('##DISPLAY_COLUMN##')->get();
EOT;
    }

    public static function getWithQueryTemplate()
    {
        return <<<'EOT'

            ->with([##RELATIONS##])
EOT;
    }

    public static function getWithCountQueryTemplate()
    {
        return <<<'EOT'

            ->withCount([##RELATIONS##])
EOT;
    }

    public function getBelongsToManyTableSlotTemplate()
    {
        return <<<'EOT'
##RELATION##->implode('##DISPLAY_COLUMN##', ',')
EOT;
    }

    public function getBelongsToTableSlotTemplate()
    {
        return <<<'EOT'
##RELATION##?->##DISPLAY_COLUMN##
EOT;
    }

    public static function getFlashComponentTemplate()
    {
        return <<<'EOT'

    @livewire('livewire-toast')
EOT;
    }

    public static function getAllColumnsTemplate()
    {
        return <<<'EOT'

    /**
     * @var array
     */
    public $columns = [##COLUMNS##];

EOT;
    }

    public static function getHideColumnInitTemplate()
    {
        return <<<'EOT'
        $this->selectedColumns = $this->columns;
EOT;
    }

    public static function getHideColumnMethodTemplate()
    {
        return <<<'EOT'


    public function showColumn($column)
    {
        return in_array($column, $this->selectedColumns);
    }
EOT;
    }

    public function getHideColumnIfTemplate()
    {
        return <<<'EOT'
@if($this->showColumn('##LABEL##'))
EOT;
    }

    public static function getBulkActionMethodTemplate()
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

    public static function getBulkActionTemplate()
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

    public static function getBulkCheckboxTemplate()
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
