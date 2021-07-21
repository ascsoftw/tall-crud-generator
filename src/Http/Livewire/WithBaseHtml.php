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

    private function _getHeaderHtml($label, $column = null, $isSortable = false)
    {
        if ($isSortable) {
            $html = Str::replace(
                [
                    '##COLUMN##',
                    '##LABEL##',
                    '##SORT_ICON_HTML##',
                ],
                [
                    $column,
                    $label,
                    $this->_getSortIconHtml($column),
                ],
                $this->_getSortingHeaderTemplate()
            );
            $slot = $this->_newLines() . $html . $this->_newLines(1, 4);
        } else {
            $slot = $label;
        }
        return $this->_getTableColumnHtml($slot);
    }

    private function _getSearchBoxHtml()
    {
        return $this->_getSearchBoxTemplate();
    }

    private function _getPaginationDropdownHtml()
    {
        return $this->_getPaginationDropdownTemplate();
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
