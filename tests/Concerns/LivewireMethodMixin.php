<?php

namespace Ascsoftw\TallCrudGenerator\Tests\Concerns;
use PHPUnit\Framework\Assert as PHPUnit;
use Closure;

class LivewireMethodMixin
{

    public function assertReturnEquals(): Closure
    {

        return function(string $method, $expected, $message = '') {
            $jsonResponse = json_decode($this->lastResponse->content());
            $actual = $jsonResponse->effects->returns->$method;
            PHPUnit::assertEquals($expected, $actual, $message);

            return $this;
        };
    }

}