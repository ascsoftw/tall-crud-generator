<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithBaseHtml
{
    public function getTableColumnHtml($slot, $params = '')
    {
        return '<x:tall-crud-generator::table-column ' . $params .  '>' . $slot . '</x:tall-crud-generator::table-column>';
    }

    public function getTableSlotHtml($f)
    {
        $preTag = $postTag = '';
        if ($this->isHideColumnsEnabled()) {
            $props = $this->getTableColumnProps($f);
            $preTag = Str::replace(
                '##LABEL##',
                $props[0],
                $this->getHideColumnIfTemplate()
            ) . $this->newLines(1, 5);
            $postTag = $this->newLines(1, 5) . '@endif';
        }

        return $preTag .
            $this->getTableColumnHtml(
                Str::replace(
                    '##COLUMN_NAME##',
                    $this->getTableSlotColumnValue($f),
                    $this->getTableColumnTemplate()
                )
            ) .
            $postTag;
    }

    public function getButtonHtml($slot, $mode = '', $params = '')
    {
        return '<x:tall-crud-generator::button mode="' . $mode . '" ' . $params . '>' . $slot . '</x:tall-crud-generator::button>';
    }

    public function getSortIconHtml($column)
    {
        return '<x:tall-crud-generator::sort-icon sortField="' . $column . '" :sort-by="$sortBy" :sort-asc="$sortAsc" />';
    }

    public function getHeaderHtml($label, $column = null, $isSortable = false)
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
                    $this->getSortIconHtml($column),
                ],
                $this->getSortingHeaderTemplate()
            );
            $slot = $this->newLines() . $html . $this->newLines(1, 4);
        } else {
            $slot = $label;
        }

        $preTag = $postTag = '';
        if ($this->isHideColumnsEnabled()) {
            $preTag = Str::replace(
                '##LABEL##',
                $label,
                $this->getHideColumnIfTemplate()
            ) . $this->newLines(1, 4);
            $postTag = $this->newLines(1, 4) . '@endif';
        }
        return $preTag . $this->getTableColumnHtml($slot) . $postTag;
    }

    public function getSelectOptionsHtml($options)
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
