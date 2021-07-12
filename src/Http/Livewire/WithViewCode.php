<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithViewCode
{
    private function _generateViewHtml()
    {
        $return = [];
        $return['css_class'] = $this->_isSearchingEnabled() ? 'justify-between' : 'justify-end';
        $return['add_link'] = $this->_generateAddLink();
        $return['search_box'] = $this->_generateSearchBox();
        $return['pagination_dropdown'] = $this->_generatePaginationDropdown();
        $return['table_header'] = $this->_generateTableHeader();
        $return['table_slot'] = $this->_generateTableSlot();
        $return['child_component'] = $this->_includeChildComponent();
        $return['flash_component'] = $this->_includeFlashComponent();
        $return['child']['delete_modal'] = $this->_generateDeleteModal();
        $return['child']['add_modal'] = $this->_generateAddModal();
        $return['child']['edit_modal'] = $this->_generateEditModal();
        return $return;
    }

    private function _generateAddLink()
    {
        if ($this->_isAddFeatureEnabled()) {
            $string = Str::replace('##COMPONENT_NAME##', $this->_getChildComponentName(), $this->_getAddButtonTemplate());
            return $this->_newLines(1, 2) . $this->_getButtonHtml($this->advancedSettings['text']['add_link'], 'add', $string);
        }
        return '';
    }

    private function _generateSearchBox()
    {
        if ($this->_isSearchingEnabled()) {
            return $this->_getSearchBoxHtml();
        }
        return '';
    }

    private function _generatePaginationDropdown()
    {
        if ($this->_isPaginationDropdownEnabled()) {
            return $this->_getPaginationDropdownHtml();
        }
        return '';
    }

    private function _generateTableHeader()
    {

        $fields = $this->_getSortedListingFields();
        $return = [];

        foreach ($fields as $f) {
            if (isset($f['isPrimaryKey']) && $f['isPrimaryKey']) {
                $return[] = $this->_getHeaderHtml($this->_getLabel($this->primaryKeyProps['label'], $this->modelProps['primary_key']), $this->modelProps['primary_key'], true);
                continue;
            }

            $return[] = $this->_getHeaderHtml($this->_getLabel($f['label'], $f['column']), $f['column']);
        }

        if ($this->_needsActionColumn()) {
            $return[] = $this->_getTableColumnHtml('Actions');
        }

        return collect($return)->implode($this->_newLines(1, 4));
    }

    private function _generateTableSlot()
    {
        $return = [];
        $fields = $this->_getSortedListingFields();

        foreach ($fields as $f) {
            if (isset($f['isPrimaryKey']) && $f['isPrimaryKey']) {
                $return[] = $this->_getTableColumnHtml(
                    Str::replace('##COLUMN_NAME##', $this->modelProps['primary_key'], $this->_getTableColumnTemplate())
                );
                continue;
            }

            $return[] = $this->_getTableColumnHtml(
                Str::replace('##COLUMN_NAME##', $f['column'], $this->_getTableColumnTemplate())
            );
        }

        if ($this->_needsActionColumn()) {
            $return[] = $this->_getTableColumnHtml($this->_getActionHtml());
        }

        return collect($return)->implode($this->_newLines(1, 5));
    }

    private function _includeChildComponent()
    {
        if ($this->_isAddFeatureEnabled() || $this->_isEditFeatureEnabled() || $this->_isDeleteFeatureEnabled()) {
            $componentName = $this->_getChildComponentName();
            return $this->_indent(1) . "@livewire('$componentName')";
        }

        return '';
    }

    private function _includeFlashComponent()
    {
        if ($this->_isFlashMessageEnabled()) {
            return $this->_indent(1) . $this->_getFlashComponentHtml();
        }

        return '';
    }

    private function _generateDeleteModal()
    {
        if (!$this->_isDeleteFeatureEnabled()) {
            return '';
        }

        return Str::replace(
            [
                '##CancelBtnText##',
                '##DeleteBtnText##',
            ],
            [
                $this->advancedSettings['text']['cancel_button'],
                $this->advancedSettings['text']['delete_button'],
            ],
            $this->_getDeleteModalTemplate()
        );
    }

    private function _generateAddModal()
    {
        if (!$this->_isAddFeatureEnabled()) {
            return '';
        }

        $fields = $this->_getSortedFormFields(true);
        $string = '';
        foreach ($fields as $field) {
            $string .=
                Str::replace(
                    [
                        '##COLUMN##',
                        '##LABEL##',
                    ],
                    [
                        $field['column'],
                        $this->_getLabel($field['label'], $field['column'])
                    ],
                    $this->_getFieldTemplate($field['attributes']['type'])
                );

            if ($field['attributes']['type'] == 'select') {
                $string = Str::replace('##OPTIONS##', $this->_getSelectOptionsHtml($field['attributes']['options']), $string);
            }
        }

        return Str::replace(
            [
                '##CancelBtnText##',
                '##CreateBtnText##',
                '##FIELDS##',
                '##BTM_FIELDS##',
                '##BELONGS_TO_FIELDS##',
            ],
            [
                $this->advancedSettings['text']['cancel_button'],
                $this->advancedSettings['text']['create_button'],
                $string,
                $this->_getBtmFields(true),
                $this->_getBelongsToFields(true),
            ],
            $this->_getAddModalTemplate()
        );
    }

    private function _generateEditModal()
    {
        if (!$this->_isEditFeatureEnabled()) {
            return '';
        }
        $fields = $this->_getSortedFormFields(false);
        $string = '';
        foreach ($fields as $field) {
            $string .=
                Str::replace(
                    [
                        '##COLUMN##',
                        '##LABEL##',
                    ],
                    [
                        $field['column'],
                        $this->_getLabel($field['label'], $field['column'])
                    ],
                    $this->_getFieldTemplate($field['attributes']['type'])
                );

            if ($field['attributes']['type'] == 'select') {
                $string = Str::replace('##OPTIONS##', $this->_getSelectOptionsHtml($field['attributes']['options']), $string);
            }
        }

        return Str::replace(
            [
                '##CancelBtnText##',
                '##EditBtnText##',
                '##FIELDS##',
                '##BTM_FIELDS##',
                '##BELONGS_TO_FIELDS##',
            ],
            [
                $this->advancedSettings['text']['cancel_button'],
                $this->advancedSettings['text']['edit_button'],
                $string,
                $this->_getBtmFields(false),
                $this->_getBelongsToFields(false),
            ],
            $this->_getEditModalTemplate()
        );
    }


    private function _getActionHtml()
    {
        $return = [];
        if ($this->_isEditFeatureEnabled()) {
            $string = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->_getChildComponentName(),
                    $this->modelProps['primary_key']
                ],
                $this->_getEditButtonTemplate()
            );
            $return[] = $this->_getButtonHtml($this->advancedSettings['text']['edit_link'], 'edit', $string);
        }

        if ($this->_isDeleteFeatureEnabled()) {
            $string = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->_getChildComponentName(),
                    $this->modelProps['primary_key']
                ],
                $this->_getDeleteButtonTemplate()
            );

            $return[] = $this->_getButtonHtml($this->advancedSettings['text']['delete_link'], 'delete', $string);
        }

        return $this->_newLines(1, 6) . collect($return)->implode($this->_newLines(1, 6)) . $this->_newLines(1, 5);
    }

    private function _getBtmFields($isAdd= true)
    {

        if( $isAdd && !$this->_isBtmAddEnabled()) {
            return '';
        }

        if( !$isAdd && !$this->_isBtmEditEnabled()) {
            return '';
        }

        $string = '';
        foreach($this->belongsToManyRelations as $r) {
            if( $isAdd && !$r['in_add']) {
                continue;
            }

            if( !$isAdd && !$r['in_edit']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##HEADING##',
                    '##RELATION##',
                    '##FIELDNAME##',
                    '##DISPLAY_COLUMN##',
                    '##RELATED_KEY##',
                ],
                [
                    Str::studly($r['relationName']),
                    $r['relationName'],
                    $this->_getBtmFieldName($r['relationName']),
                    $r['displayColumn'],
                    $r['relatedKey'],
                ],
                $this->_getBtmFieldTemplate()
            );
        }
        return $string;
    }

    private function _getBelongsToFields($isAdd= true)
    {
        if( $isAdd && !$this->_isBelongsToAddEnabled()) {
            return '';
        }

        if( !$isAdd && !$this->_isBelongsToEditEnabled()) {
            return '';
        }

        $string = '';
        foreach($this->belongsToRelations as $r) {
            if( $isAdd && !$r['in_add']) {
                continue;
            }

            if( !$isAdd && !$r['in_edit']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##LABEL##',
                    '##COLUMN##',
                    '##BELONGS_TO_VAR##',
                    '##OWNER_KEY##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    Str::ucfirst($r['relationName']),
                    $r['column'],
                    Str::plural($r['relationName']),
                    $r['ownerKey'],
                    $r['displayColumn'],
                ],
                $this->_getBelongsToFieldTemplate()
            );
        }
        return $string;
    }
}
