<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Illuminate\Support\Str;

class ChildComponentCode extends BaseCode
{
    use WithTemplates;

    public $tallProperties;

    public function __construct($tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getDeleteCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getDeleteFeatureFlag()) {
            $code['vars'] = $this->getDeleteVars();
            $code['method'] = $this->getDeleteMethod();
        }

        return $code;
    }

    public function getAddCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getAddFeatureFlag()) {
            $code['vars'] = $this->getAddVars();
            $code['method'] = $this->getAddMethod();
        }

        return $code;
    }

    public function getEditCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getEditFeatureFlag()) {
            $code['vars'] = $this->getEditVars();
            $code['method'] = $this->getEditMethod();
        }

        return $code;
    }

    public function getChildListenersCode()
    {
        if (!($this->tallProperties->getAddFeatureFlag() ||
            $this->tallProperties->getDeleteFeatureFlag() ||
            $this->tallProperties->getEditFeatureFlag())) {
            return '';
        }

        return str_replace(
            [
                '##DELETE_LISTENER##',
                '##ADD_LISTENER##',
                '##EDIT_LISTENER##',
            ],
            [
                $this->tallProperties->getDeleteFeatureFlag() ? 'showDeleteForm' : '',
                $this->tallProperties->getAddFeatureFlag() ? 'showCreateForm' : '',
                $this->tallProperties->getEditFeatureFlag() ? 'showEditForm' : '',
            ],
            WithTemplates::getChildListenerTemplate(),
        );
    }

    public function getChildItemCode()
    {
        return WithTemplates::getChildItemTemplate();
    }

    public function getDeleteVars()
    {
        return WithTemplates::getDeleteVarsTemplate();
    }

    public function getAddVars()
    {
        return WithTemplates::getAddVarsTemplate();
    }

    public function getEditVars()
    {
        return WithTemplates::getEditVarsTemplate();
    }

    public function getDeleteMethod()
    {
        return str_replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
            ],
            [
                $this->tallProperties->getModelName(),
                $this->tallProperties->getComponentName(),
                $this->getDeleteFlashCode(),
            ],
            WithTemplates::getDeleteMethodTemplate()
        );
    }

    public function getAddMethod()
    {

        return str_replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##CREATE_FIELDS##',
                '##FLASH_MESSAGE##',
                '##BTM_INIT##',
                '##BTM_ATTACH##',
                '##BELONGS_TO_INIT##',
                '##BELONGS_TO_SAVE##',
            ],
            [
                $this->tallProperties->getModelName(),
                $this->tallProperties->getComponentName(),
                $this->getAddFieldsCode(),
                $this->getAddFlashCode(),
                $this->getBtmInitCode(),
                $this->getBtmAttachCode(),
                $this->getBelongsToInitCode(),
                $this->getBelongsToSaveCode(),
            ],
            WithTemplates::getAddMethodTemplate()
        );
    }

    public function getEditMethod()
    {
        return str_replace(
            [
                '##MODEL##',
                '##MODEL_VAR##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
                '##BTM_FETCH##',
                '##BTM_UPDATE##',
                '##BELONGS_TO_INIT##',
            ],
            [
                $this->tallProperties->getModelName(),
                Str::lower($this->tallProperties->getModelName()),
                $this->tallProperties->getComponentName(),
                $this->getEditFlashCode(),
                $this->getBtmFetchCode(),
                $this->getBtmUpdateCode(),
                $this->getBelongsToInitCode(false),
            ],
            $this->getEditMethodTemplate()
        );
    }

    public function getAddFieldsCode()
    {
        $addFields = $this->tallProperties->getSelfFAddFields();

        return $addFields->map(function ($field) {
            return str_replace(
                [
                    '##COLUMN##',
                    '##DEFAULT_VALUE##',
                ],
                [
                    $field['column'],
                    ($field['attributes']['type'] == 'checkbox') ? '0' : "''",
                ],
                WithTemplates::getAddFieldTemplate()
            );
        })->prependAndJoin($this->newLines(1, 3));

    }

    public function getBtmInitCode()
    {
        $btmFields = $this->tallProperties->getBtmAddFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function($r) {
            return str_replace(
                [
                    '##RELATION##',
                    '##MODEL##',
                    '##FIELD_NAME##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $r['relationName'],
                    $this->tallProperties->getModelName($r['modelPath']),
                    $this->getBtmFieldName($r['relationName']),
                    $r['displayColumn'],
                ],
                WithTemplates::getBtmInitTemplate()
            );
        })->implode('');

    }

    public function getBtmAttachCode()
    {
        $btmFields = $this->tallProperties->getBtmAddFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function($r) {
            return str_replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName']),
                ],
                WithTemplates::getBtmAttachTemplate()
            );
        })->implode('') . $this->newLines();

    }

    public function getBtmFetchCode()
    {
        $btmFields = $this->tallProperties->getBtmEditFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function ($r) {
            return str_replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                    '##KEY##',
                    '##MODEL##',
                    '##MODEL_VAR##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName']),
                    $r['relatedKey'],
                    $this->tallProperties->getModelName($r['modelPath']),
                    Str::lower($this->tallProperties->getModelName()),
                    $r['displayColumn'],
                ],
                WithTemplates::getBtmFetchTemplate()
            );
        })->prependAndJoin('', $this->newLines());
    }

    public function getBtmUpdateCode()
    {
        $btmFields = $this->tallProperties->getBtmEditFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function( $r) {
            return str_replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName']),
                ],
                WithTemplates::getBtmUpdateTemplate()
            );
        })->prependAndJoin($this->newLines());

    }

    public function getBelongsToInitCode($isAdd = true)
    {
        if ($isAdd) {
            $belongsToFields = $this->tallProperties->getBelongsToAddFields();
        } else {
            $belongsToFields = $this->tallProperties->getBelongsToEditFields();
        }

        if ($belongsToFields->isEmpty()) {
            return '';
        }

        return $belongsToFields->map(function($r) {
            return str_replace(
                [
                    '##BELONGS_TO_VAR##',
                    '##MODEL##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $this->getBelongsToVarName($r['relationName']),
                    $this->tallProperties->getModelName($r['modelPath']),
                    $r['displayColumn'],
                ],
                WithTemplates::getBelongsToInitTemplate()
            );
        })->prependAndJoin($this->newLines());

    }

    public function getBelongsToSaveCode()
    {
        $belongsToFields = $this->tallProperties->getBelongsToAddFields();

        if ($belongsToFields->isEmpty()) {
            return '';
        }

        return $belongsToFields->map(function($r) {
            return str_replace(
                [
                    '##COLUMN##',
                    '##DEFAULT_VALUE##',
                ],
                [
                    $r['foreignKey'],
                    0,
                ],
                WithTemplates::getAddFieldTemplate()
            );
        })->prependAndJoin($this->newLines(1, 3));

    }

    public function getAddFlashCode()
    {
        return $this->getFlashCode($this->tallProperties->getFlashMessageText('add'));
    }

    public function getEditFlashCode()
    {
        return $this->getFlashCode($this->tallProperties->getFlashMessageText('edit'));
    }

    public function getDeleteFlashCode()
    {
        return $this->getFlashCode($this->tallProperties->getFlashMessageText('delete'));
    }

    public function getFlashCode($message)
    {
        if (empty($message) || !$this->tallProperties->getFlashMessageFlag()) {
            return '';
        }

        return str_replace(
            '##MESSAGE##',
            $message,
            WithTemplates::getFlashTemplate()
        );
    }

    public function getBelongsToVarName($relation)
    {
        return Str::plural($relation);
    }
}
