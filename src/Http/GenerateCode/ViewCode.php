<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallProperties;

class ViewCode extends BaseCode
{
    use WithTemplates;

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
            WithTemplates::getAddButtonTemplate()
        );
    }

    public function getSearchBox()
    {
        if (!$this->tallProperties->isSearchingEnabled()) {
            return '';
        }

        return WithTemplates::getSearchBoxTemplate();
    }

    public function getPaginationDropdown()
    {
        if (!$this->tallProperties->isPaginationDropdownEnabled()) {
            return '';
        }

        return WithTemplates::getPaginationDropdownTemplate();
    }

    public function getHideColumnsDropdown()
    {
        if (!$this->tallProperties->isHideColumnsEnabled()) {
            return '';
        }

        return WithTemplates::getHideColumnDropdownTemplate();
    }

    public function getBulkActionDropdown()
    {
        if (!$this->tallProperties->isBulkActionsEnabled()) {
            return '';
        }

        return WithTemplates::getBulkActionTemplate();
    }

    public function getFilterDropdown()
    {
        if (!$this->tallProperties->isFilterEnabled()) {
            return'';
        }

        return WithTemplates::getFilterDropdownTemplate();
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

        return WithTemplates::getFlashComponentTemplate();

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
                WithTemplates::getEditButtonTemplate()
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
                WithTemplates::getDeleteButtonTemplate()
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
                WithTemplates::getBulkCheckboxTemplate()
            );

    }

    public function getTableColumnHtml($slot, $params = '')
    {
        return '<td class="'.$this->tallProperties->getTableClasses('td').'"'.$params.'>'.$slot.'</td>';
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
                $this->getSortingHeaderTemplate()
            );
            $slot = $this->newLines().$html.$this->newLines(1, 4);
        } else {
            $slot = $label;
        }

        $preTag = $postTag = '';
        if ($this->tallProperties->isHideColumnsEnabled()) {
            $preTag = str_replace(
                '##LABEL##',
                $label,
                $this->getHideColumnIfTemplate()
            ).$this->newLines(1, 4);
            $postTag = $this->newLines(1, 4).'@endif';
        }

        return $preTag.$this->getTableColumnHtml($slot).$postTag;
    }

    public function getTableSlotHtml($f)
    {
        $preTag = $postTag = '';
        if ($this->tallProperties->isHideColumnsEnabled()) {
            $preTag = str_replace(
                '##LABEL##',
                $f['label'],
                $this->getHideColumnIfTemplate()
            ).$this->newLines(1, 5);
            $postTag = $this->newLines(1, 5).'@endif';
        }

        return $preTag.
            $this->getTableColumnHtml(
                str_replace(
                    '##COLUMN_NAME##',
                    $f['slot'],
                    $this->getTableColumnTemplate()
                )
            ).
            $postTag;
    }
}
