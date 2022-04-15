<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Illuminate\Support\Str;

class ChildComponentCode extends BaseCode
{
    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getDeleteCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->isDeleteFeatureEnabled()) {
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
        if ($this->tallProperties->isAddFeatureEnabled()) {
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
        if ($this->tallProperties->isEditFeatureEnabled()) {
            $code['vars'] = $this->getEditVars();
            $code['method'] = $this->getEditMethod();
        }

        return $code;
    }

    public function getListenersArray()
    {
        if (! ($this->tallProperties->isAddFeatureEnabled() ||
            $this->tallProperties->isDeleteFeatureEnabled() ||
            $this->tallProperties->isEditFeatureEnabled())) {
            return '';
        }

        return str_replace(
            [
                '##DELETE_LISTENER##',
                '##ADD_LISTENER##',
                '##EDIT_LISTENER##',
            ],
            [
                $this->tallProperties->isDeleteFeatureEnabled() ? 'showDeleteForm' : '',
                $this->tallProperties->isAddFeatureEnabled() ? 'showCreateForm' : '',
                $this->tallProperties->isEditFeatureEnabled() ? 'showEditForm' : '',
            ],
            Template::getListenerArray(),
        );
    }

    // public function getChildItemCode()
    // {
    //     return Template::getChildItemTemplate();
    // }

    public function getRulesArray()
    {
        $fields = $this->tallProperties->getSelfFormFields();
        $rules = collect();

        foreach ($fields as $field) {
            $rules->push(
                $this->getKeyValueCode(
                    $field['column'],
                    Str::of($field['fieldAttributes']['rules'])->explode(',')->filter()->join('|')
                )
            );
        }

        $rules->push($this->getRulesForBelongsToFields());

        return str_replace(
            '##RULES##',
            $rules->filter()->prependAndJoin($this->newLines(1, 2)),
            Template::getRulesArray()
        );
    }

    public function getValidationAttributes()
    {
        $fields = $this->tallProperties->getSelfFormFields();
        $attributes = collect();
        foreach ($fields as $field) {
            $attributes->push(
                $this->getKeyValueCode(
                    $field['column'],
                    $this->getLabel($field['label'], $field['column'])
                )
            );
        }

        $attributes->push($this->getAttributesForBelongsToFields());

        return str_replace(
            '##ATTRIBUTES##',
            $attributes->filter()->prependAndJoin($this->newLines(1, 2)),
            Template::getValidationAttributesArray()
        );
    }

    public function getOtherModelsCode()
    {
        $fields = $this->tallProperties->getBtmRelations()->merge($this->tallProperties->getBelongsToRelations());

        return $fields->map(function ($r) {
            return $this->getUseModelCode($r['modelPath']);
        })->unique()->implode('');
    }

    public function getRelationVars()
    {
        return $this->getBtmVars() . $this->getBelongsToVars();
    }

    public function getDeleteVars()
    {
        return Template::getDeleteVariables();
    }

    public function getAddVars()
    {
        return Template::getAddVariables();
    }

    public function getEditVars()
    {
        return Template::getEditVariables();
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
            Template::getDeleteFeatureCode()
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
            Template::getAddFeatureCode()
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
            Template::getEditFeatureCode()
        );
    }

    public function getAddFieldsCode()
    {
        $addFields = $this->tallProperties->getSelfAddFields();

        return $addFields->map(function ($field) {
            return str_replace(
                [
                    '##COLUMN##',
                    '##DEFAULT_VALUE##',
                ],
                [
                    $field['column'],
                    ($field['fieldAttributes']['type'] == 'checkbox') ? '0' : "''",
                ],
                Template::getAddFieldTemplate()
            );
        })->prependAndJoin($this->newLines(1, 3));
    }

    public function getBtmInitCode()
    {
        $btmFields = $this->tallProperties->getBtmAddFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function ($r) {
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
                Template::getBtmInitCode()
            );
        })->implode('');
    }

    public function getBtmAttachCode()
    {
        $btmFields = $this->tallProperties->getBtmAddFields();
        if ($btmFields->isEmpty()) {
            return '';
        }

        return $btmFields->map(function ($r) {
            return str_replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName']),
                ],
                Template::getBtmAttachCode()
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
                Template::getBtmFetchCode()
            );
        })->prependAndJoin('', $this->newLines());
    }

    public function getBtmUpdateCode()
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
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName']),
                ],
                Template::getBtmUpdateCode()
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

        return $belongsToFields->map(function ($r) {
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
                Template::getBelongsToInitCode()
            );
        })->prependAndJoin($this->newLines());
    }

    public function getBelongsToSaveCode()
    {
        $belongsToFields = $this->tallProperties->getBelongsToAddFields();

        if ($belongsToFields->isEmpty()) {
            return '';
        }

        return $belongsToFields->map(function ($r) {
            return str_replace(
                [
                    '##COLUMN##',
                    '##DEFAULT_VALUE##',
                ],
                [
                    $r['foreignKey'],
                    0,
                ],
                Template::getAddFieldTemplate()
            );
        })->prependAndJoin($this->newLines(1, 3));
    }

    public function getRulesForBelongsToFields()
    {
        $fields = $this->tallProperties->getBelongsToRelations();

        return $fields->map(function ($r) {
            return $this->getKeyValueCode($r['foreignKey'], 'required');
        })->join($this->newLines(1, 2));
    }

    public function getAttributesForBelongsToFields()
    {
        $fields = $this->tallProperties->getBelongsToRelations();

        return $fields->map(function ($r) {
            return $this->getKeyValueCode(
                $r['foreignKey'],
                Str::ucfirst($r['relationName'])
            );
        })->join($this->newLines(1, 2));
    }

    public function getBtmVars()
    {
        $relations = $this->tallProperties->getBtmRelations();
        if ($relations->isEmpty()) {
            return '';
        }

        return $relations->map(function ($r) {
            return self::getEmtpyArray($r['relationName']) .
                self::getEmtpyArray(
                    $this->getBtmFieldName($r['relationName'])
                );
        })->prependAndJoin($this->newLines());
    }

    public function getBelongsToVars()
    {
        $relations = $this->tallProperties->getBelongsToRelations();
        if ($relations->isEmpty()) {
            return '';
        }

        return $relations->map(function ($r) {
            return self::getEmtpyArray(
                $this->getBelongsToVarName($r['relationName'])
            );
        })->prependAndJoin($this->newLines());
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
        if (empty($message) || ! $this->tallProperties->isFlashMessageEnabled()) {
            return '';
        }

        return str_replace(
            '##MESSAGE##',
            $message,
            Template::getFlashCode()
        );
    }

    public function getBelongsToVarName($relation)
    {
        return Str::plural($relation);
    }

    public function getKeyValueCode($columnName, $value)
    {
        return str_replace(
            [
                '##COLUMN_NAME##',
                '##VALUE##',
            ],
            [
                $columnName,
                $value,
            ],
            Template::getKeyValueTemplate()
        );
    }
}
