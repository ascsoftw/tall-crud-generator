<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithBaseHtml
{
    private function _getTableColumnHtml($slot)
    {
        return '<x:tall-crud-generator::table-column>' . $slot . '</x:tall-crud-generator::table-column>';
    }

    private function _getButtonHtml($slot, $mode = '', $params = '')
    {
        return '<x:tall-crud-generator::button mode="' . $mode . '" ' . $params . '>' . $slot . '</x:tall-crud-generator::button>';
    }

    private function _getSortIconHtml($column)
    {
        return '<x:tall-crud-generator::sort-icon sortField="' . $column . '" :sort-by="$sortBy" :sort-asc="$sortAsc" />';
    }

    private function _getHeaderHtml($label, $column, $isPrimaryKey = false)
    {
        if (($isPrimaryKey && $this->_isPrimaryKeySortable()) || $this->_isColumnSortable($column)) {
            $sortIconHtml = $this->_getSortIconHtml($column);
            $html = Str::replace(
                [
                    '##COLUMN##',
                    '##LABEL##',
                    '##SORT_ICON_HTML##',
                ],
                [
                    $column,
                    $label,
                    $sortIconHtml,
                ],
                $this->_getSortingHeaderTemplate()
            );
            $slot = $this->_newLines() . $html . $this->_newLines(1, 4);
            return $this->_getTableColumnHtml($slot);
        }
        return $this->_getTableColumnHtml($label);
    }

    private function _getSearchBoxHtml()
    {
        return $this->_getSearchBoxTemplate();
    }

    private function _getSelectOptionsHtml($options)
    {
        $options = json_decode($options);
        if (is_null($options)) {
            return '';
        }

        $html = '';
        foreach ($options as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }
        return $html;
    }
}
