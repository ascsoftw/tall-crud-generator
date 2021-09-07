<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Illuminate\Support\Str;

class BaseCode
{
    use WithTemplates;

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
}