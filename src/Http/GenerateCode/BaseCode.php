<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Illuminate\Support\Str;

class BaseCode
{
    use WithTemplates;

    public function getUseModelCode($modelPath)
    {
        return str_replace(
            '##MODEL##',
            $modelPath,
            WithTemplates::getUseModelTemplate()
        );
    }

    public function wrapInQuotesAndJoin($collection, $glue = ',')
    {
        return $collection->map(function ($m) {
            return Str::of($m)->append("'")->prepend("'");
        })->implode($glue);
    }

    public static function getEmtpyArray($name, $type = 'array')
    {
        return str_replace(
            [
                '##NAME##',
                '##TYPE##',
            ],
            [
                $name,
                $type,
            ],
            WithTemplates::getEmptyArrayTemplate()
        );
    }

    public function newLines($count = 1, $indent = 0)
    {
        return str_repeat("\n".$this->indent($indent), $count);
    }

    public function spaces($count = 1)
    {
        return str_repeat(' ', $count);
    }

    public function indent($step = 1)
    {
        return $this->spaces($step * 4);
    }

    public function getBtmFieldName($relation)
    {
        return 'checked'.Str::studly($relation);
    }

    public function getLabel($label = '', $column = '')
    {
        if (! empty($label)) {
            return $label;
        }

        return Str::title(str_replace('_', ' ', $column));
    }

    public function getButtonHtml($slot, $mode = '', $params = '')
    {
        return '<x:tall-crud-generator::button mode="'.$mode.'" '.$params.'>'.$slot.'</x:tall-crud-generator::button>';
    }

    public function getSortIconHtml($column)
    {
        return '<x:tall-crud-generator::sort-icon sortField="'.$column.'" :sort-by="$sortBy" :sort-asc="$sortAsc" />';
    }
}