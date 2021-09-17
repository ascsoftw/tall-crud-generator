<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildViewCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ViewCode;
use Illuminate\Support\Facades\App;

trait WithViewCode
{
    public function generateViewHtml()
    {
        $code = [];
        $this->viewCode = App::make(ViewCode::class);
        $code['add_link'] = $this->viewCode->getAddLink();
        $code['search_box'] = $this->viewCode->getSearchBox();
        $code['pagination_dropdown'] = $this->viewCode->getPaginationDropdown();
        $code['hide_columns'] = $this->viewCode->getHideColumnsDropdown();
        $code['bulk_action'] = $this->viewCode->getBulkActionDropdown();
        $code['filter_dropdown'] = $this->viewCode->getFilterDropdown();
        $code['table_header'] = $this->viewCode->getTableHeader();
        $code['table_slot'] = $this->viewCode->getTableSlot();
        $code['child_component'] = $this->viewCode->getChildComponent();
        $code['flash_component'] = $this->viewCode->getFlashComponent();
        $code['classes'] = $this->viewCode->getTableClasses();

        $this->childViewCode = App::make(ChildViewCode::class);
        $code['child']['delete_modal'] = $this->childViewCode->getDeleteModal();
        $code['child']['add_modal'] = $this->childViewCode->getAddModal();
        $code['child']['edit_modal'] = $this->childViewCode->getEditModal();

        return $code;
    }
}
