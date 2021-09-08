<?php

namespace Ascsoftw\TallCrudGenerator\Tests\Concerns;

use Closure;
use PHPUnit\Framework\Assert as PHPUnit;

class LivewireMethodMixin
{
    // public function assertReturnEquals(): Closure
    // {
    //     return function (string $method, $expected, $message = '') {
    //         $jsonResponse = json_decode($this->lastResponse->content());
    //         $actual = $jsonResponse->effects->returns->$method;
    //         PHPUnit::assertEquals($expected, $actual, $message);

    //         return $this;
    //     };
    // }

    // public function assertReturnCount()
    // {
    //     return function (string $method, $expected, $message = '') {
    //         $jsonResponse = json_decode($this->lastResponse->content());
    //         $actual = $jsonResponse->effects->returns->$method;
    //         PHPUnit::assertCount($expected, $actual, $message);

    //         return $this;
    //     };
    // }

    public function pressNext()
    {
        return function ($times = 1) {
            for ($i = 1; $i <= $times; $i++) {
                $this->call('moveAhead');
            }

            return $this;
        };
    }

    public function step1()
    {
        return function () {
            $this->set('modelPath', 'Ascsoftw\TallCrudGenerator\Tests\Models\Product')
                ->pressNext();

            return $this;
        };
    }

    public function disableModals()
    {
        return function ($enableFeature = false) {
            $this->set('componentProps.createAddModal', $enableFeature)
                ->set('componentProps.createEditModal', $enableFeature)
                ->set('componentProps.createDeleteButton', $enableFeature);

            return $this;
        };
    }

    public function hidePrimaryKeyFromListing()
    {
        return function ($enable = false) {
            $this->set('primaryKeyProps.inList', $enable);

            return $this;
        };
    }

    public function makePrimaryKeyUnsortable()
    {
        return function ($enable = false) {
            $this->set('primaryKeyProps.sortable', $enable);

            return $this;
        };
    }

    public function setStandardFields()
    {
        return function () {
            $this->call('addField')
                ->set('fields.0.column', 'name')
                ->set('fields.0.searchable', true)
                ->set('fields.0.sortable', true)
                ->set('fields.0.attributes.rules', 'required,min:3,max:50,')
                ->call('addField')
                ->set('fields.1.column', 'price')
                ->set('fields.0.sortable', true)
                ->set('fields.1.attributes.rules', 'required,numeric,')
                ->call('addField')
                ->set('fields.2.column', 'sku')
                ->set('fields.2.attributes.rules', 'required,min:3,')
                ->call('addField')
                ->set('fields.3.column', 'status')
                ->set('fields.3.label', 'Is Active')
                ->set('fields.3.inList', false)
                ->set('fields.3.attributes.type', 'checkbox');

            return $this;
        };
    }

    public function eagerLoadStandardRelations()
    {
        return function () {
            $this->call('createNewWithRelation')
                ->set('withRelation.name', 'brand')
                ->set('withRelation.displayColumn', 'name')
                ->call('addWithRelation')
                ->call('createNewWithRelation')
                ->set('withRelation.name', 'categories')
                ->set('withRelation.displayColumn', 'name')
                ->call('addWithRelation')
                ->call('createNewWithRelation')
                ->set('withRelation.name', 'tags')
                ->set('withRelation.displayColumn', 'name')
                ->call('addWithRelation');

            return $this;
        };
    }

    public function eagerLoadCountStandardRelations()
    {
        return function () {
            $this->call('createNewWithCountRelation')
                ->set('withCountRelation.name', 'tags')
                ->call('addWithCountRelation')
                ->call('createNewWithCountRelation')
                ->set('withCountRelation.name', 'categories')
                ->call('addWithCountRelation');
            return $this;
        };
    }

    public function disableFlashMessage()
    {
        return function ($enable = false) {
            $this->set('flashMessages.enable', $enable);

            return $this;
        };
    }

    public function disablePaginationDropdown()
    {
        return function ($enable = false) {
            $this->set('advancedSettings.table_settings.showPaginationDropdown', $enable);

            return $this;
        };
    }

    public function generateFiles()
    {
        return function () {
            $this->set('componentName', 'products')
                ->pressNext();

            return $this;
        };
    }
}
