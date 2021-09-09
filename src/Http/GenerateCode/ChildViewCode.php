<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Illuminate\Support\Str;

class ChildViewCode extends BaseCode
{
    use WithTemplates;

    public $tallProperties;

    public function __construct($tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getDeleteModal()
    {
        if (!$this->tallProperties->getDeleteFeatureFlag()) {
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
