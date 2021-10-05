<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Illuminate\Support\Str;

class ChildViewCode extends BaseCode
{
    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getDeleteModal()
    {
        if (! $this->tallProperties->isDeleteFeatureEnabled()) {
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
            Template::getDeleteModal()
        );
    }

    public function getAddModal()
    {
        if (! $this->tallProperties->isAddFeatureEnabled()) {
            return '';
        }

        $fields = $this->tallProperties->getAddFormFields();
        $fieldsHtml = collect();
        foreach ($fields as $field) {
            $fieldsHtml->push($this->getFieldHtml($field));
        }

        return str_replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##CREATE_BTN_TEXT##',
                '##FIELDS##',
            ],
            [
                $this->tallProperties->getAdvancedSettingsText('cancelButton'),
                $this->tallProperties->getAdvancedSettingsText('createButton'),
                $fieldsHtml->implode(''),
            ],
            Template::getAddModal()
        );
    }

    public function getEditModal()
    {
        if (! $this->tallProperties->isEditFeatureEnabled()) {
            return '';
        }
        $fields = $this->tallProperties->getEditFormFields();
        $fieldsHtml = collect();
        foreach ($fields as $field) {
            $fieldsHtml->push($this->getFieldHtml($field));
        }

        return str_replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##EDIT_BTN_TEXT##',
                '##FIELDS##',
            ],
            [
                $this->tallProperties->getAdvancedSettingsText('cancelButton'),
                $this->tallProperties->getAdvancedSettingsText('editButton'),
                $fieldsHtml->implode(''),
            ],
            Template::getEditModal()
        );
    }

    public function getFieldHtml($field)
    {
        switch ($field['type']) {
            case 'normal':
                return $this->getNormalFieldHtml($field);
            case 'btm':
                return $this->getBtmFieldHtml($field);
            case 'belongsTo':
                return $this->getBelongsToFieldHtml($field);
        }

        return '';
    }

    public function getNormalFieldHtml($field)
    {
        switch ($field['attributes']['type']) {
            case 'checkbox':
                $fieldTemplate = Template::getCheckboxField();

                break;
            case 'select':
                $fieldTemplate = Template::getSelectField();

                break;
            case 'input':
            default:
                $fieldTemplate = Template::getInputField();
        }

        $html = str_replace(
            [
                '##COLUMN##',
                '##LABEL##',
            ],
            [
                $field['column'],
                $this->getLabel($field['label'], $field['column']),
            ],
            $fieldTemplate
        );

        if ($field['attributes']['type'] == 'select') {
            $html = str_replace(
                '##OPTIONS##',
                $this->getSelectOptionsHtml($field['attributes']['options']),
                $html
            );
        }

        return $html;
    }

    public function getBtmFieldHtml($r)
    {
        return str_replace(
            [
                '##HEADING##',
                '##RELATION##',
                '##FIELD_NAME##',
                '##DISPLAY_COLUMN##',
                '##RELATED_KEY##',
            ],
            [
                Str::studly($r['relationName']),
                $r['relationName'],
                $this->getBtmFieldName($r['relationName']),
                $r['displayColumn'],
                $r['relatedKey'],
            ],
            $r['isMultiSelect'] ? Template::getBtmFieldMultiSelectTemplate() : Template::getBtmFieldTemplate()
        );
    }

    public function getBelongsToFieldHtml($r)
    {
        return str_replace(
            [
                '##LABEL##',
                '##FOREIGN_KEY##',
                '##BELONGS_TO_VAR##',
                '##OWNER_KEY##',
                '##DISPLAY_COLUMN##',
            ],
            [
                Str::ucfirst($r['relationName']),
                $r['foreignKey'],
                Str::plural($r['relationName']),
                $r['ownerKey'],
                $r['displayColumn'],
            ],
            Template::getBelongsToFieldTemplate()
        );
    }

    public function getSelectOptionsHtml($options)
    {
        $options = json_decode($options);
        if (is_null($options)) {
            return '';
        }

        $html = '';
        foreach ($options as $key => $value) {
            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }

        return $html;
    }
}
