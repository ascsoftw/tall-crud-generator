<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

class ViewCode extends BaseCode
{
    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getAddLink()
    {
        if (!$this->tallProperties->isAddFeatureEnabled()) {
            return '';
        }

        return str_replace(
            '##COMPONENT_NAME##',
            $this->tallProperties->getChildComponentName(),
            Template::getAddButton()
        );
    }

    public function getSearchBox()
    {
        if (!$this->tallProperties->isSearchingEnabled()) {
            return '';
        }

        return Template::getSearchInputField();
    }

    public function getPaginationDropdown()
    {
        if (!$this->tallProperties->isPaginationDropdownEnabled()) {
            return '';
        }

        return Template::getPaginationSelectElement();
    }

    public function getHideColumnsDropdown()
    {
        if (!$this->tallProperties->isHideColumnsEnabled()) {
            return '';
        }

        return Template::getHideColumnsDropdown();
    }

    public function getBulkActionDropdown()
    {
        if (!$this->tallProperties->isBulkActionsEnabled()) {
            return '';
        }

        return Template::getBulkActionDropdown();
    }

    public function getFilterDropdown()
    {
        if (!$this->tallProperties->isFilterEnabled()) {
            return'';
        }

        return Template::getFilterDropdownTemplate();
    }

    public function getChildComponent()
    {
        if (!($this->tallProperties->isAddFeatureEnabled() ||
            $this->tallProperties->isDeleteFeatureEnabled() ||
            $this->tallProperties->isEditFeatureEnabled())) {
            return '';
        }
        $componentName = $this->tallProperties->getChildComponentName();

        return $this->indent(1)."@livewire('$componentName')";

    }

    public function getFlashComponent()
    {
        if (!$this->tallProperties->isFlashMessageEnabled()) {
            return '';
        }

        return Template::getFlashComponent();

    }

    public function getTableHeader()
    {
        $columns = $this->tallProperties->getListingColumns();
        $headers = collect();

        if ($this->tallProperties->isBulkActionsEnabled()) {
            $headers->push($this->getTableColumnHtml('', 'width="10"'));
        }

        foreach ($columns as $column) {
            $headers->push($this->getHeaderHtml($column['label'], $column['column'], $column['isSortable']));
        }

        if ($this->tallProperties->needsActionColumn()) {
            $headers->push($this->getTableColumnHtml('Actions'));
        }

        return $headers->implode($this->newLines(1, 4));
    }

    public function getTableSlot()
    {
        $columns = $this->tallProperties->getListingColumns();

        $slot = collect();

        if ($this->tallProperties->isBulkActionsEnabled()) {
            $slot->push(
                $this->getTableColumnHtml(
                    $this->getBulkColumnCheckbox()
                )
            );
        }

        foreach ($columns as $column) {
            $slot->push($this->getTableSlotHtml($column));
        }

        if ($this->tallProperties->needsActionColumn()) {
            $slot->push(
                $this->getTableColumnHtml(
                    $this->getActionHtml()
                )
            );
        }

        return $slot->implode($this->newLines(1, 5));
    }

    public function getTableClasses()
    {
        $classes = [
            'th' => '',
            'trBottomBorder' => '',
            'trHover' => '',
            'trEven' => '',
        ];

        $classes['th'] = $this->tallProperties->getTableClasses('th');
        $classes['trBottomBorder'] = $this->tallProperties->getTableClasses('trBottomBorder');
        if (!empty($this->tallProperties->getTableClasses('trEven'))) {
            $classes['trEven'] = '{{ ($loop->even ) ? "' . $this->tallProperties->getTableClasses('trEven') . '" : ""}}';
        }
        if (!empty($this->tallProperties->getTableClasses('trHover'))) {
            $classes['trHover'] = 'hover:' . $this->tallProperties->getTableClasses('trHover');
        }

        return $classes;

    }

    public function getActionHtml()
    {
        $buttons = collect();
        if ($this->tallProperties->isEditFeatureEnabled()) {
            $buttons->push(str_replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->tallProperties->getChildComponentName(),
                    $this->tallProperties->getPrimaryKey(),
                ],
                Template::getEditButton()
            ));
        }

        if ($this->tallProperties->isDeleteFeatureEnabled()) {
            $buttons->push(str_replace(
                [
                    '##COMPONENT_NAME##',
                    '##PRIMARY_KEY##',
                ],
                [
                    $this->tallProperties->getChildComponentName(),
                    $this->tallProperties->getPrimaryKey(),
                ],
                Template::getDeleteButton()
            ));
        }

        return $buttons->join('').$this->newLines(1, 5);
    }

    public function getBulkColumnCheckbox()
    {
        return 
            str_replace(
                '##PRIMARY_KEY##',
                $this->tallProperties->getPrimaryKey(),
                Template::getBulkColumnCheckbox()
            );

    }

    public function getTableColumnHtml($slot, $params = '')
    {
        return '<td class="'.$this->tallProperties->getTableClasses('td').'" '.$params.'>'.$slot.'</td>';
    }

    public function getSortIconHtml($column)
    {
        return '<x:tall-crud-generator::sort-icon sortField="'.$column.'" :sort-by="$sortBy" :sort-asc="$sortAsc" />';
    }

    public function getHeaderHtml($label, $column = null, $isSortable = false)
    {
        if ($isSortable) {
            $html = str_replace(
                [
                    '##COLUMN##',
                    '##LABEL##',
                    '##SORT_ICON_HTML##',
                ],
                [
                    $column,
                    $label,
                    $this->getSortIconHtml($column),
                ],
                Template::getSortableHeader()
            );
            $slot = $this->newLines().$html.$this->newLines(1, 4);
        } else {
            $slot = $label;
        }

        return $this->encapsulateTableColumn($this->getTableColumnHtml($slot), $label, 4);
    }

    public function getTableSlotHtml($f)
    {

        if($f['type'] == 'with')
        {
            $slot = $this->getTableSlotForEagerLoad($f);
        } else {
            $slot = $f['slot'];
        }

        $html = $this->getTableColumnHtml(
                str_replace(
                    '##COLUMN_NAME##',
                    $slot,
                    Template::getTableColumnSlot()
                )
        );
        return $this->encapsulateTableColumn($html, $f['label'], 5);
    }

    public function encapsulateTableColumn($slot, $label, $indent = 4)
    {
        if (!$this->tallProperties->isHideColumnsEnabled()) {
            return $slot;
        }

        $preTag = str_replace(
            '##LABEL##',
            $label,
            Template::getHideColumnIfCondition()
        ).$this->newLines(1, $indent);
        $postTag = $this->newLines(1, $indent).'@endif';

        return $preTag . $slot . $postTag;
    }

    public function getTableSlotForEagerLoad($field)
    {
        if ($field['isBelongsToRelation']) {
            return str_replace(
                [
                    '##RELATION##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $field['relationName'],
                    $field['displayColumn'],
                ],
                Template::getBelongsToTableSlot()
            );
        }

        return str_replace(
            [
                '##RELATION##',
                '##DISPLAY_COLUMN##',
            ],
            [
                $field['relationName'],
                $field['displayColumn'],
            ],
            Template::getBtmTableSlot()
        );
    }

    
}
