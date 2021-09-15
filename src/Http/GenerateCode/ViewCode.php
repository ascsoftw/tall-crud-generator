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
        if (!$this->tallProperties->getAddFeatureFlag()) {
            return '';
        }

        $buttonParams = str_replace(
            '##COMPONENT_NAME##',
            $this->tallProperties->getChildComponentName(),
            WithTemplates::getAddButtonTemplate()
        );

        return $this->newLines(1, 2).
            $this->getButtonHtml(
                $this->tallProperties->getAdvancedSettingsText('addLink'),
                'add',
                $buttonParams
            );
    }

    public function getSearchBox()
    {
        if (!$this->tallProperties->getSearchingFlag()) {
            return '';
        }

        return WithTemplates::getSearchBoxTemplate();
    }

    public function getPaginationDropdown()
    {
        if (!$this->tallProperties->getPaginationDropdownFlag()) {
            return '';
        }

        return WithTemplates::getPaginationDropdownTemplate();
    }

    public function getHideColumnsDropdown()
    {
        if (!$this->tallProperties->getHideColumnsFlag()) {
            return '';
        }

        return WithTemplates::getHideColumnDropdownTemplate();
    }

    public function getBulkActionDropdown()
    {
        if (!$this->tallProperties->getBulkActionFlag()) {
            return '';
        }

        return WithTemplates::getBulkActionTemplate();
    }

    public function getFilterDropdown()
    {
        if (!$this->tallProperties->getFilterFlag()) {
            return'';
        }

        return WithTemplates::getFilterDropdownTemplate();
    }
}
