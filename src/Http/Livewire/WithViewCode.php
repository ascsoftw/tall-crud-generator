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
        $return['table_header'] = $this->_generateTableHeader();
        $return['table_slot'] = $this->_generateTableSlot();
        $return['child_component'] = $this->_includeChildComponent();
        $return['child']['delete_modal'] = $this->_generateDeleteModal();
        $return['child']['add_modal'] = $this->_generateAddModal();
        $return['child']['edit_modal'] = $this->_generateEditModal();
        return $return;
    }

    private function _generateAddLink()
    {
        if ($this->_isAddFeatureEnabled()) {
            $string = Str::replace('##COMPONENT_NAME##', $this->_getChildComponentName(), $this->_getAddButtonTemplate());
            return $this->_newLines(1, 3) . $this->_getButtonHtml($this->advancedSettings['text']['add_link'], 'add', $string);
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

    private function _generateTableHeader()
    {
        $return = [];

        if ($this->_needsPrimaryKeyInListing()) {
            $return[] = $this->_getHeaderHtml($this->_getLabel($this->primaryKeyProps['label'], $this->modelProps['primary_key']), $this->modelProps['primary_key'], true);
        }

        foreach ($this->fields as $f) {
            if ($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) {
                $return[] = $this->_getHeaderHtml($this->_getLabel($f['label'], $f['column']), $f['column']);
            }
        }

        if ($this->_needsActionColumn()) {
            $return[] = $this->_getTableColumnHtml('Actions');
        }

        return collect($return)->implode($this->_newLines(1, 4));
    }

    private function _generateTableSlot()
    {
        $return = [];

        if ($this->_needsPrimaryKeyInListing()) {
            $return[] = $this->_getTableColumnHtml(
                Str::replace('##COLUMN_NAME##', $this->modelProps['primary_key'], $this->_getTableColumnTemplate())
            );
        }

        foreach ($this->fields as $f) {
            if ($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) {
                $return[] = $this->_getTableColumnHtml(
                    Str::replace('##COLUMN_NAME##', $f['column'], $this->_getTableColumnTemplate())
                );
            }
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

        $fields = $this->_getFormFields(true, false);
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
            ],
            [
                $this->advancedSettings['text']['cancel_button'],
                $this->advancedSettings['text']['create_button'],
                $string,
            ],
            $this->_getAddModalTemplate()
        );
    }

    private function _generateEditModal()
    {
        if (!$this->_isEditFeatureEnabled()) {
            return '';
        }
        $fields = $this->_getFormFields(false, true);
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
            ],
            [
                $this->advancedSettings['text']['cancel_button'],
                $this->advancedSettings['text']['edit_button'],
                $string,
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
}
