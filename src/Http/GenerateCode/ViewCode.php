<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallProperties;

class ViewCode extends BaseCode
{
    use WithTemplates;

    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getAddLink()
    {
        if (!$this->tallProperties->isAddFeatureEnabled()) {
            return '';
        }

        return str_replace(
            '##COMPONENT_NAME##',
            $this->tallProperties->getChildComponentName(),
            WithTemplates::getAddButtonTemplate()
        );
    }

    public function getSearchBox()
    {
        if (!$this->tallProperties->isSearchingEnabled()) {
            return '';
        }

        return WithTemplates::getSearchBoxTemplate();
    }

    public function getPaginationDropdown()
    {
        if (!$this->tallProperties->isPaginationDropdownEnabled()) {
            return '';
        }

        return WithTemplates::getPaginationDropdownTemplate();
    }

    public function getHideColumnsDropdown()
    {
        if (!$this->tallProperties->isHideColumnsEnabled()) {
            return '';
        }

        return WithTemplates::getHideColumnDropdownTemplate();
    }

    public function getBulkActionDropdown()
    {
        if (!$this->tallProperties->isBulkActionsEnabled()) {
            return '';
        }

        return WithTemplates::getBulkActionTemplate();
    }

    public function getFilterDropdown()
    {
        if (!$this->tallProperties->isFilterEnabled()) {
            return'';
        }

        return WithTemplates::getFilterDropdownTemplate();
    }
}
