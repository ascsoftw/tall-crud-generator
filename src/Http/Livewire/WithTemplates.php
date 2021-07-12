<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

trait WithTemplates
{
    private function _getSortingHeaderTemplate()
    {
        return <<<'EOT'
                    <div class="flex items-center">
                        <button wire:click="sortBy('##COLUMN##')">##LABEL##</button>
                        ##SORT_ICON_HTML##
                    </div>
EOT;
    }

    private function _getSearchBoxTemplate()
    {
        return <<<EOT

            <div class="flex">
                <input wire:model.debounce.500ms="q" type="search" placeholder="Search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                <span class="ml-3 mt-2" wire:loading.delay wire:target="q">
                    <x:tall-crud-generator::loading-indicator />
                </span>
            </div>
EOT;
    }

    private function _getPaginationDropdownTemplate()
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

    private function _getDeleteButtonTemplate()
    {
        return <<<'EOT'
wire:click="$emitTo('##COMPONENT_NAME##', 'showDeleteForm',  {{ $result->##PRIMARY_KEY##}});"
EOT;
    }

    private function _getAddButtonTemplate()
    {
        return <<<'EOT'
wire:click="$emitTo('##COMPONENT_NAME##', 'showCreateForm');"
EOT;
    }

    private function _getEditButtonTemplate()
    {
        return <<<'EOT'
wire:click="$emitTo('##COMPONENT_NAME##', 'showEditForm',  {{ $result->##PRIMARY_KEY##}});"
EOT;
    }

    private function _getDeleteMethodTemplate()
    {
        return <<<'EOT'


    public function showDeleteForm($id)
    {
        $this->confirmingItemDeletion = true;
        $this->primaryKey = $id;
    }

    public function deleteItem()
    {
        ##MODEL##::destroy($this->primaryKey);
        $this->confirmingItemDeletion = false;
        $this->primaryKey = '';
        $this->reset(['item']);
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    private function _getAddMethodTemplate()
    {
        return <<<'EOT'
 
    public function showCreateForm()
    {
        $this->confirmingItemCreation = true;
        $this->resetErrorBag();
        $this->reset(['item']);##BTM_INIT##
    }

    public function createItem() 
    {
        $this->validate();
        $item = ##MODEL##::create([##CREATE_FIELDS##
        ]);##BTM_ATTACH##
        $this->confirmingItemCreation = false;
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    private function _getCreateFieldTemplate()
    {
        return <<<'EOT'
'##COLUMN##' => $this->item['##COLUMN##'] ?? ##DEFAULT_VALUE##, 
EOT;
    }

    private function _getEditMethodTemplate()
    {
        return <<<'EOT'
 
    public function showEditForm(##MODEL## $##MODEL_VAR##)
    {
        $this->resetErrorBag();
        $this->item = $##MODEL_VAR##;
        $this->confirmingItemEdition = true;##BTM_FETCH##
    }

    public function editItem() 
    {
        $this->validate();
        $this->item->save();##BTM_UPDATE##
        $this->confirmingItemEdition = false;
        $this->primaryKey = '';
        $this->emitTo('##COMPONENT_NAME##', 'refresh');##FLASH_MESSAGE##
    }

EOT;
    }

    private function _getChildListenerTemplate()
    {
        return <<<'EOT'
    protected $listeners = [
        '##DELETE_LISTENER##',
        '##ADD_LISTENER##',
        '##EDIT_LISTENER##',
    ];

EOT;
    }

    private function _getSortingMethodTemplate()
    {
        return <<<'EOT'


    public function sortBy($field)
    {
        if ($field == $this->sortBy) {
            $this->sortAsc = !$this->sortAsc;
        }
        $this->sortBy = $field;
    }
EOT;
    }

    private function _getSearchingMethodTemplate()
    {
        return <<<'EOT'


    public function updatingQ() 
    {
        $this->resetPage();
    }
EOT;
    }

    private function _getPaginationDropdownMethodTemplate()
    {
        return <<<'EOT'


    public function updatingPerPage() 
    {
        $this->resetPage();
    }
EOT;
    }

    private function _getTableColumnTemplate()
    {
        return <<<'EOT'
{{ $result->##COLUMN_NAME##}}
EOT;
    }

    private function _getSortingVarsTemplate()
    {
        return <<<'EOT'


    public $sortBy = '##SORT_COLUMN##';
    public $sortAsc = true;

EOT;
    }

    private function _getSortingQueryTemplate()
    {
        return <<<'EOT'

            ->orderBy($this->sortBy, $this->sortAsc ? 'ASC' : 'DESC')
EOT;
    }

    private function _getDeleteVarsTemplate()
    {
        return <<<'EOT'

    public $confirmingItemDeletion = false;
    public $primaryKey;
EOT;
    }

    private function _getAddVarsTemplate()
    {
        return <<<'EOT'

    public $confirmingItemCreation = false;
EOT;
    }

    private function _getEditVarsTemplate()
    {
        return <<<'EOT'

    public $confirmingItemEdition = false;
EOT;
    }

    private function _getSearchingVarsTemplate()
    {
        return <<<'EOT'

    public $q;
EOT;
    }

    private function _getPaginationVarsTemplate()
    {
        return <<<'EOT'

    public $per_page = ##PER_PAGE##;
EOT;
    }

    private function _getSearchinQueryTemplate()
    {
        return <<<'EOT'

            ->when($this->q, function ($query) {
                return $query->where(function ($query) {
                    ##SEARCH_QUERY##;
                });
            })
EOT;
    }

    private function _getSearchingQueryWhereTemplate()
    {
        return <<<'EOT'
##FIRST##('##COLUMN##', 'like', '%' . $this->q . '%')
EOT;
    }

    private function _getChildItemTemplate()
    {
        return <<<'EOT'

    public $item;
EOT;
    }

    private function _getChildRulesTemplate()
    {
        return <<<'EOT'


    protected $rules = [##RULES##
    ];
EOT;
    }

    private function _getChildFieldTemplate()
    {
        return <<<'EOT'
'item.##COLUMN_NAME##' => '##VALUE##',
EOT;
    }

    private function _getchildValidationAttributesTemplate()
    {
        return <<<'EOT'


    protected $validationAttributes = [##ATTRIBUTES##
    ];

EOT;
    }

    private function _getDeleteModalTemplate()
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
            <x:tall-crud-generator::button wire:click="$set('confirmingItemDeletion', false)">##CancelBtnText##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="delete" wire:loading.attr="disabled" wire:click="deleteItem()">##DeleteBtnText##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::confirmation-dialog>

EOT;
    }

    private function _getAddModalTemplate()
    {
        return <<<'EOT'

    <x:tall-crud-generator::dialog-modal wire:model="confirmingItemCreation">
        <x-slot name="title">
            Add Record
        </x-slot>

        <x-slot name="content">##FIELDS####BTM_FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemCreation', false)">##CancelBtnText##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="add" wire:loading.attr="disabled" wire:click="createItem()">##CreateBtnText##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::dialog-modal>

EOT;
    }

    private function _getEditModalTemplate()
    {
        return <<<'EOT'

    <x:tall-crud-generator::dialog-modal wire:model="confirmingItemEdition">
        <x-slot name="title">
            Edit Record
        </x-slot>

        <x-slot name="content">##FIELDS####BTM_FIELDS##
        </x-slot>

        <x-slot name="footer">
            <x:tall-crud-generator::button wire:click="$set('confirmingItemEdition', false)">##CancelBtnText##</x:tall-crud-generator::button>
            <x:tall-crud-generator::button mode="add" wire:loading.attr="disabled" wire:click="editItem()">##EditBtnText##</x:tall-crud-generator::button>
        </x-slot>
    </x:tall-crud-generator::dialog-modal>
EOT;
    }

    private function _getInputFieldTemplate()
    {
        return <<<'EOT'

            <div class="mt-4">
                <x:tall-crud-generator::label>##LABEL##</x:tall-crud-generator::label>
                <x:tall-crud-generator::input class="block mt-1 w-1/2" type="text" wire:model.defer="item.##COLUMN##" />
                @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
            </div>
EOT;
    }

    private function _getSelectFieldTemplate()
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

    private function _getCheckboxFieldTemplate()
    {
        return <<<'EOT'

            <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                <x:tall-crud-generator::label>##LABEL##:</x:tall-crud-generator::label><x:tall-crud-generator::checkbox class="ml-2" wire:model.defer="item.##COLUMN##" />
            </x:tall-crud-generator::checkbox-wrapper>
            @error('item.##COLUMN##') <x:tall-crud-generator::error-message>{{$message}}</x:tall-crud-generator::error-message> @enderror
EOT;
    }

    private function _getFieldTemplate($type = 'input')
    {
        switch ($type) {
            case 'checkbox':
                return $this->_getCheckboxFieldTemplate();
            case 'select':
                return $this->_getSelectFieldTemplate();
            case 'input':
            default:
                return $this->_getInputFieldTemplate();
        }
    }

    private function _getFlashTriggerTemplate()
    {
        return <<<'EOT'

        $this->emitTo('livewire-toast', 'show', '##MESSAGE##');
EOT;
    }

    private function _getArrayTemplate()
    {
        return <<<'EOT'
    public $##NAME## = [];
EOT;
    }

    private function _getBtmInitTemplate()
    {
        return <<<'EOT'


        $this->##RELATION## = ##MODEL##::all();
        $this->##FIELDNAME## = [];
EOT;
    }

    private function _getBtmAttachTemplate()
    {
        return <<<'EOT'

        $item->##RELATION##()->attach($this->##FIELDNAME##);
EOT;
    }

    private function _getBtmFetchTemplate()
    {
        return <<<'EOT'

        $this->##FIELDNAME## = $##MODEL_VAR##->##RELATION##->pluck("##KEY##")->map(function( $i) {
            return (string)$i;
        })->toArray();
        $this->##RELATION## = ##MODEL##::all();

EOT;
    }

    private function _getBtmUpdateTemplate()
    {
        return <<<'EOT'

        $this->item->##RELATION##()->sync($this->##FIELDNAME##);
        $this->##FIELDNAME## = [];

EOT;
    }

    private function _getOtherModelTemplate()
    {
        return <<<'EOT'

use ##MODEL##;
EOT;
    }

    private function _getBtmFieldTemplate()
    {

        return <<<'EOT'


            <h2 class="mt-4">##HEADING##</h2>
            <div class="grid grid-cols-3">
                @foreach( $##RELATION## as $c)
                <x:tall-crud-generator::checkbox-wrapper class="mt-4">
                    <x:tall-crud-generator::label>{{$c->##DISPLAY_COLUMN##}}</x:tall-crud-generator::label>
                    <x:tall-crud-generator::checkbox value="{{ $c->##RELATED_KEY## }}" class="ml-2" wire:model.defer="##FIELDNAME##" />
                </x:tall-crud-generator::checkbox-wrapper>
                @endforeach
            </div>
EOT;
    }
}
