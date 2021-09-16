<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildViewCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ViewCode;
use Illuminate\Support\Facades\App;

trait WithViewCode
{
    public function generateViewHtml()
    {
        $code = [];
        $this->viewCode = App::make(ViewCode::class);
        $code['add_link'] = $this->viewCode->getAddLink();
        $code['search_box'] = $this->viewCode->getSearchBox();
        $code['pagination_dropdown'] = $this->viewCode->getPaginationDropdown();
        $code['hide_columns'] = $this->viewCode->getHideColumnsDropdown();
        $code['bulk_action'] = $this->viewCode->getBulkActionDropdown();
        $code['filter_dropdown'] = $this->viewCode->getFilterDropdown();
        $code['table_header'] = $this->generateTableHeader();
        $code['table_slot'] = $this->generateTableSlot();
        $code['child_component'] = $this->includeChildComponent();
        $code['flash_component'] = $this->includeFlashComponent();
        $code['classes'] = $this->getTableClasses();

        $this->childViewCode = App::make(ChildViewCode::class);
        $code['child']['delete_modal'] = $this->childViewCode->getDeleteModal();
        $code['child']['add_modal'] = $this->generateAddModal();
        $code['child']['edit_modal'] = $this->generateEditModal();

        return $code;
    }

    public function generateTableHeader()
    {
        $fields = $this->getSortedListingFields();
        $headers = collect();

        if ($this->isBulkActionsEnabled()) {
            $headers->push($this->getTableColumnHtml('', 'width="10"'));
        }

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

        if ($this->isBulkActionsEnabled()) {
            $columns->push(
                $this->getTableColumnHtml(
                    $this->getBulkColumnCheckbox()
                )
            );
        }

        foreach ($fields as $f) {
            $columns->push($this->getTableSlotHtml($f));
        }

        if ($this->needsActionColumn()) {
            $columns->push(
                $this->getTableColumnHtml(
                    $this->getActionHtml()
                )
            );
        }

        return $columns->implode($this->newLines(1, 5));
    }

    public function includeChildComponent()
    {
        if ($this->isAddFeatureEnabled() || $this->isEditFeatureEnabled() || $this->isDeleteFeatureEnabled()) {
            $componentName = $this->getChildComponentName();

            return $this->indent(1)."@livewire('$componentName')";
        }

        return '';
    }

    public function includeFlashComponent()
    {
        if ($this->isFlashMessageEnabled()) {
            return $this->newLines(1, 1).$this->getFlashComponentTemplate();
        }

        return '';
    }

    public function generateAddModal()
    {
        if (! $this->isAddFeatureEnabled()) {
            return '';
        }

        $fields = $this->getSortedFormFields(true);
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
                $this->advancedSettings['text']['cancelButton'],
                $this->advancedSettings['text']['createButton'],
                $fieldsHtml->implode(''),
            ],
            $this->getAddModalTemplate()
        );
    }

    public function generateEditModal()
    {
        if (! $this->isEditFeatureEnabled()) {
            return '';
        }
        $fields = $this->getSortedFormFields(false);
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
            $buttons->push(str_replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->getPrimaryKey(),
                ],
                $this->getEditButtonTemplate()
            ));
        }

        if ($this->isDeleteFeatureEnabled()) {
            $buttons->push(str_replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->getChildComponentName(),
                    $this->getPrimaryKey(),
                ],
                $this->getDeleteButtonTemplate()
            ));
        }

        return $buttons->join('').$this->newLines(1, 5);
    }

    public function getBulkColumnCheckbox()
    {
        $html = collect();
        $html->push(
            str_replace(
                '##PRIMARY_KEY##',
                $this->getPrimaryKey(),
                $this->getBulkCheckboxTemplate()
            )
        );

        return $html->prependAndJoin($this->newLines(1, 6)).$this->newLines(1, 5);
    }

    public function getWithTableSlot($r)
    {
        if ($this->isBelongsToManyRelation($r['relationName']) || $this->isHasManyRelation($r['relationName'])) {
            return str_replace(
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

        return str_replace(
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
        $html = str_replace(
            [
                '##COLUMN##',
                '##LABEL##',
            ],
            [
                $field['column'],
                $this->getLabel($field['label'], $field['column']),
            ],
            $this->getFieldTemplate($field['attributes']['type'])
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
            $r['isMultiSelect'] ? $this->getBtmFieldMultiSelectTemplate() : $this->getBtmFieldTemplate()
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

    public function getTableClasses()
    {
        $classes = [
            'th' => '',
            'trBottomBorder' => '',
            'trHover' => '',
            'trEven' => '',
        ];

        $classes['th'] = $this->advancedSettings['table_settings']['thClass'];
        $classes['trBottomBorder'] = $this->advancedSettings['table_settings']['trBottomBorder'];
        if (!empty($this->advancedSettings['table_settings']['trEvenClass'])) {
            $classes['trEven'] = '{{ ($loop->even ) ? "' . $this->advancedSettings['table_settings']['trEvenClass'] . '" : ""}}';
        }
        if (!empty($this->advancedSettings['table_settings']['trHoverClass'])) {
            $classes['trHover'] = 'hover:' . $this->advancedSettings['table_settings']['trHoverClass'];
        }

        return $classes;

    }
}
