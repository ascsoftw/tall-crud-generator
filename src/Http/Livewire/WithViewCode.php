<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithViewCode
{
    public function generateViewHtml()
    {
        $code = [];
        $code['css_class'] = $this->isSearchingEnabled() ? 'justify-between' : 'justify-end';
        $code['add_link'] = $this->generateAddLink();
        $code['search_box'] = $this->generateSearchBox();
        $code['pagination_dropdown'] = $this->generatePaginationDropdown();
        $code['hide_columns'] = $this->generateHideColumnsDropdown();
        $code['table_header'] = $this->generateTableHeader();
        $code['table_slot'] = $this->generateTableSlot();
        $code['child_component'] = $this->includeChildComponent();
        $code['flash_component'] = $this->includeFlashComponent();
        $code['child']['delete_modal'] = $this->generateDeleteModal();
        $code['child']['add_modal'] = $this->generateAddModal();
        $code['child']['edit_modal'] = $this->generateEditModal();
        return $code;
    }

    public function generateAddLink()
    {
        if ($this->isAddFeatureEnabled()) {
            $buttonParams = Str::replace(
                '##COMPONENT_NAME##',
                $this->getChildComponentName(),
                $this->getAddButtonTemplate()
            );
            return $this->newLines(1, 2) .
                $this->getButtonHtml(
                    $this->advancedSettings['text']['addLink'],
                    'add',
                    $buttonParams
                );
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

    public function generateHideColumnsDropdown()
    {
        if ($this->isHideColumnsEnabled()) {
            return $this->getHideColumnDropdownHtml();
        }
        return '';
    }

    public function generateTableHeader()
    {

        $fields = $this->getSortedListingFields();
        $headers = collect();

        foreach ($fields as $f) {
            [$label, $column, $isSortable] = $this->getTableColumnProps($f);
            $headers->push($this->getHeaderHtml($label, $column, $isSortable));
        }

        if ($this->needsActionColumn()) {
            $headers->push($this->getTableColumnHtml('Actions'));
        }

        return $headers->implode($this->newLines(1, 4));
    }

    public function generateTableSlot()
    {
        $fields = $this->getSortedListingFields();
        $columns = collect();

        foreach ($fields as $f) {
            $preTag = $postTag = '';
            if ($this->isHideColumnsEnabled()) {
                [$label, $column, $isSortable] = $this->getTableColumnProps($f);
                $preTag = Str::replace(
                    '##LABEL##',
                    $label,
                    $this->getHideColumnIfTemplate()
                ) . $this->newLines(1, 5);
                $postTag = $this->newLines(1, 5) . '@endif';
            }
            $columns->push($preTag . $this->getTableColumnHtml(
                Str::replace(
                    '##COLUMN_NAME##',
                    $this->getTableSlotColumnValue($f),
                    $this->getTableColumnTemplate()
                )
            ) . $postTag);
        }

        if ($this->needsActionColumn()) {
            $columns->push($this->getTableColumnHtml(
                $this->getActionHtml()
            ));
        }

        return $columns->implode($this->newLines(1, 5));
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
        $fieldsHtml = collect();
        foreach ($fields as $field) {
            $fieldsHtml->push($this->getFieldHtml($field));
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
                $fieldsHtml->implode(''),
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
        $fieldsHtml = collect();
        foreach ($fields as $field) {
            $fieldsHtml->push($this->getFieldHtml($field));
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
                $fieldsHtml->implode(''),
            ],
            $this->getEditModalTemplate()
        );
    }

    public function getActionHtml()
    {
        $buttons = collect();
        if ($this->isEditFeatureEnabled()) {
            $buttonParams = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->getPrimaryKey()
                ],
                $this->getEditButtonTemplate()
            );
            $buttons->push($this->getButtonHtml(
                $this->advancedSettings['text']['editLink'],
                'edit',
                $buttonParams
            ));
        }

        if ($this->isDeleteFeatureEnabled()) {
            $buttonParams = Str::replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->getPrimaryKey()
                ],
                $this->getDeleteButtonTemplate()
            );

            $buttons->push($this->getButtonHtml(
                $this->advancedSettings['text']['deleteLink'],
                'delete',
                $buttonParams
            ));
        }

        return $this->newLines(1, 6) . $buttons->implode($this->newLines(1, 6)) . $this->newLines(1, 5);
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
        $html = Str::replace(
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
            $html = Str::replace(
                '##OPTIONS##',
                $this->getSelectOptionsHtml($field['attributes']['options']),
                $html
            );
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
                return $this->getPrimaryKey();
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

    public function getTableColumnProps($field)
    {
        switch ($field['type']) {
            case 'primary':
                $label = $this->getLabel($this->primaryKeyProps['label'], $this->getPrimaryKey());
                $column = $this->getPrimaryKey();
                $isSortable = $this->isPrimaryKeySortable();
                break;
            case 'normal':
                $label = $this->getLabel($field['label'], $field['column']);
                $column = $field['column'];
                $isSortable = $this->isColumnSortable($field['column']);
                break;
            case 'with':
                $label = $this->getLabelForWith($field['relationName']);
                $column = null;
                $isSortable = false;
                break;
            case 'withCount':
                $label = $this->getLabelForWithCount($field['relationName']);
                $column = $this->getColumnForWithCount($field['relationName']);
                $isSortable = $field['isSortable'];
                break;
        }

        return [$label, $column, $isSortable];
    }
}
