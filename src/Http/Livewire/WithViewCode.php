<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithViewCode
{
    public function generateViewHtml()
    {
        $return = [];
        $return['css_class'] = $this->isSearchingEnabled() ? 'justify-between' : 'justify-end';
        $return['add_link'] = $this->generateAddLink();
        $return['search_box'] = $this->generateSearchBox();
        $return['pagination_dropdown'] = $this->generatePaginationDropdown();
        $return['table_header'] = $this->generateTableHeader();
        $return['table_slot'] = $this->generateTableSlot();
        $return['child_component'] = $this->includeChildComponent();
        $return['flash_component'] = $this->includeFlashComponent();
        $return['child']['delete_modal'] = $this->generateDeleteModal();
        $return['child']['add_modal'] = $this->generateAddModal();
        $return['child']['edit_modal'] = $this->generateEditModal();
        return $return;
    }

    public function generateAddLink()
    {
        if ($this->isAddFeatureEnabled()) {
            $string = Str::replace('##COMPONENT_NAME##', $this->getChildComponentName(), $this->getAddButtonTemplate());
            return $this->newLines(1, 2) . $this->getButtonHtml($this->advancedSettings['text']['addLink'], 'add', $string);
        }
        return '';
    }

    public function generateSearchBox()
    {
        if ($this->isSearchingEnabled()) {
            return $this->getSearchBoxHtml();
        }
        return '';
    }

    public function generatePaginationDropdown()
    {
        if ($this->isPaginationDropdownEnabled()) {
            return $this->getPaginationDropdownHtml();
        }
        return '';
    }

    public function generateTableHeader()
    {

        $fields = $this->getSortedListingFields();
        $return = [];

        foreach ($fields as $f) {
            $label = '';
            $column = null;
            $isSortable = false;

            switch ($f['type']) {
                case 'primary':
                    $label = $this->getLabel($this->primaryKeyProps['label'], $this->modelProps['primary_key']);
                    $column = $this->modelProps['primary_key'];
                    $isSortable = $this->isPrimaryKeySortable();
                    break;
                case 'normal':
                    $label = $this->getLabel($f['label'], $f['column']);
                    $column = $f['column'];
                    $isSortable = $this->isColumnSortable($f['column']);
                    break;
                case 'with':
                    $label = $this->getLabelForWith($f['relationName']);
                    break;
                case 'withCount':
                    $label = $this->getLabelForWithCount($f['relationName']);
                    $column = $this->getColumnForWithCount($f['relationName']);
                    $isSortable = $f['isSortable'];
                    break;
            }
            $return[] = $this->getHeaderHtml($label, $column, $isSortable);
        }

        if ($this->needsActionColumn()) {
            $return[] = $this->getTableColumnHtml('Actions');
        }

        return collect($return)->implode($this->newLines(1, 4));
    }

    public function generateTableSlot()
    {
        $fields = $this->getSortedListingFields();
        $return = [];

        foreach ($fields as $f) {
            $return[] = $this->getTableColumnHtml(
                Str::replace('##COLUMN_NAME##', $this->getTableSlotColumnValue($f), $this->getTableColumnTemplate())
            );
        }

        if ($this->needsActionColumn()) {
            $return[] = $this->getTableColumnHtml($this->getActionHtml());
        }

        return collect($return)->implode($this->newLines(1, 5));
    }

    public function includeChildComponent()
    {
        if ($this->isAddFeatureEnabled() || $this->isEditFeatureEnabled() || $this->isDeleteFeatureEnabled()) {
            $componentName = $this->getChildComponentName();
            return $this->indent(1) . "@livewire('$componentName')";
        }

        return '';
    }

    public function includeFlashComponent()
    {
        if ($this->isFlashMessageEnabled()) {
            return $this->indent(1) . $this->getFlashComponentTemplate();
        }

        return '';
    }

    public function generateDeleteModal()
    {
        if (!$this->isDeleteFeatureEnabled()) {
            return '';
        }

        return Str::replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##DELETE_BTN_TEXT##',
            ],
            [
                $this->advancedSettings['text']['cancelButton'],
                $this->advancedSettings['text']['deleteButton'],
            ],
            $this->getDeleteModalTemplate()
        );
    }

    public function generateAddModal()
    {
        if (!$this->isAddFeatureEnabled()) {
            return '';
        }

        $fields = $this->getSortedFormFields(true);
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->getFieldHtml($field);
        }

        return Str::replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##CREATE_BTN_TEXT##',
                '##FIELDS##',
            ],
            [
                $this->advancedSettings['text']['cancelButton'],
                $this->advancedSettings['text']['createButton'],
                $string,
            ],
            $this->getAddModalTemplate()
        );
    }

    public function generateEditModal()
    {
        if (!$this->isEditFeatureEnabled()) {
            return '';
        }
        $fields = $this->getSortedFormFields(false);
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->getFieldHtml($field);
        }

        return Str::replace(
            [
                '##CANCEL_BTN_TEXT##',
                '##EDIT_BTN_TEXT##',
                '##FIELDS##',
            ],
            [
                $this->advancedSettings['text']['cancelButton'],
                $this->advancedSettings['text']['editButton'],
                $string,
            ],
            $this->getEditModalTemplate()
        );
    }

    public function getActionHtml()
    {
        $return = [];
        if ($this->isEditFeatureEnabled()) {
            $string = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->modelProps['primary_key']
                ],
                $this->getEditButtonTemplate()
            );
            $return[] = $this->getButtonHtml($this->advancedSettings['text']['editLink'], 'edit', $string);
        }

        if ($this->isDeleteFeatureEnabled()) {
            $string = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->modelProps['primary_key']
                ],
                $this->getDeleteButtonTemplate()
            );

            $return[] = $this->getButtonHtml($this->advancedSettings['text']['deleteLink'], 'delete', $string);
        }

        return $this->newLines(1, 6) . collect($return)->implode($this->newLines(1, 6)) . $this->newLines(1, 5);
    }

    public function getWithTableSlot($r)
    {
        if ($this->isBelongsToManyRelation($r['relationName']) || $this->isHasManyRelation($r['relationName'])) {
            return Str::replace(
                [
                    '##RELATION##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $r['relationName'],
                    $r['displayColumn'],
                ],
                $this->getBelongsToManyTableSlotTemplate()
            );
        }

        return Str::replace(
            [
                '##RELATION##',
                '##DISPLAY_COLUMN##',
            ],
            [
                $r['relationName'],
                $r['displayColumn'],
            ],
            $this->getBelongsToTableSlotTemplate()
        );
    }

    public function getNormalFieldHtml($field)
    {
        $html =
            Str::replace(
                [
                    '##COLUMN##',
                    '##LABEL##',
                ],
                [
                    $field['column'],
                    $this->getLabel($field['label'], $field['column'])
                ],
                $this->getFieldTemplate($field['attributes']['type'])
            );

        if ($field['attributes']['type'] == 'select') {
            $html = Str::replace('##OPTIONS##', $this->getSelectOptionsHtml($field['attributes']['options']), $html);
        }
        return $html;
    }

    public function getBtmFieldHtml($r)
    {
        return Str::replace(
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
            $this->getBtmFieldTemplate()
        );
    }

    public function getBelongsToFieldHtml($r)
    {
        return Str::replace(
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
            $this->getBelongsToFieldTemplate()
        );
    }

    public function getTableSlotColumnValue($f)
    {
        switch ($f['type']) {
            case 'primary':
                return $this->modelProps['primary_key'];
            case 'normal':
                return  $f['column'];
            case 'with':
                return $this->getWithTableSlot($f);
            case 'withCount':
                return $this->getColumnForWithCount($f['relationName']);
        }
        return '';
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
}
