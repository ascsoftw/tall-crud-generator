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

    public function assertReturnCount()
    {
        return function(string $method, $expected, $message = '') {
            $jsonResponse = json_decode($this->lastResponse->content());
            $actual = $jsonResponse->effects->returns->$method;
            PHPUnit::assertCount($expected, $actual, $message);

            return $this;
        };
    }


    public function pressNext()
    {
        return function($times = 1) {
            for($i = 1; $i <= $times; $i++) {
                $this->call('moveAhead');
            }
            return $this;
        };
    }

    public function step1()
    {
        return function() {
            $this->set('modelPath', 'Ascsoftw\TallCrudGenerator\Tests\Models\Product')
                ->pressNext();
            return $this;
        };
    }

    public function disableModals()
    {
        return function($enableFeature = false) {
            $this->set('componentProps.createAddModal', $enableFeature)
                ->set('componentProps.createEditModal', $enableFeature)
                ->set('componentProps.createDeleteButton', $enableFeature);
            return $this;
        };
    }

    public function hidePrimaryKeyFromListing()
    {
        return function($enable = false) {
            $this->set('primaryKeyProps.inList', $enable);
            return $this;
        };
    }

    public function makePrimaryKeyUnsortable()
    {
        return function($enable = false) {
            $this->set('primaryKeyProps.sortable', $enable);
            return $this;
        };
    }

    public function disableFlashMessage()
    {
        return function($enable = false) {
            $this->set('flashMessages.enable', $enable);
            return $this;
        };
    }

    public function disablePaginationDropdown()
    {
        return function($enable = false) {
            $this->set('advancedSettings.table_settings.showPaginationDropdown', $enable);
            return $this;
        };
    }

    public function generateFiles()
    {
        return function() {
            $this->set('componentName', 'products')
                ->pressNext();
            return $this;
        };
    }
}