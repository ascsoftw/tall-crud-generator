<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallProperties;

class ChildViewCode extends BaseCode
{
    use WithTemplates;

    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getDeleteModal()
    {
        if (!$this->tallProperties->isDeleteFeatureEnabled()) {
            return '';
        }

        return str_replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##DELETE_BTN_TEXT##',
            ],
            [
                $this->tallProperties->getAdvancedSettingsText('cancelButton'),
                $this->tallProperties->getAdvancedSettingsText('deleteButton'),
            ],
            WithTemplates::getDeleteModalTemplate()
        );
    }
}
