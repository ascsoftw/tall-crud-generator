<?php

namespace Ascsoftw\TallCrudGenerator\Tests\Concerns;

class LivewireMethodMixin
{
    public function pressNext()
    {
        return function ($times = 1) {
            for ($i = 1; $i <= $times; $i++) {
                $this->call('moveAhead');
            }

            return $this;
        };
    }

    public function finishStep1()
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

    public function setStandardFilters()
    {
        return function ($isMultiple = false) {
            $this->call('createNewFilter')
                ->set('filter.type', 'None')
                ->set('filter.column', 'name')
                ->call('addFilter')

                ->call('createNewFilter')
                ->set('filter.type', 'BelongsTo')
                ->set('filter.relation', 'brand')
                ->set('filter.column', 'name')
                ->set('filter.isMultiple', $isMultiple)
                ->call('addFilter')

                ->call('createNewFilter')
                ->set('filter.type', 'BelongsToMany')
                ->set('filter.relation', 'categories')
                ->set('filter.column', 'name')
                ->set('filter.isMultiple', $isMultiple)
                ->call('addFilter');

            return $this;
        };
    }

    public function setStandardDateFilters()
    {
        return function () {
            $this->call('createNewFilter')
                ->set('filter.type', 'Date')
                ->set('filter.column', 'created_at')
                ->set('filter.label', 'Created From')
                ->call('addFilter')

                ->call('createNewFilter')
                ->set('filter.type', 'Date')
                ->set('filter.column', 'created_at')
                ->set('filter.label', 'Created Till')
                ->set('filter.operator', '<=')
                ->call('addFilter');

            return $this;
        };
    }

    public function setStandardBelongsToRelation()
    {
        return function () {
            $this->call('createNewBelongsToRelation')
                ->set('belongsToRelation.name', 'brand')
                ->set('belongsToRelation.displayColumn', 'name')
                ->call('addBelongsToRelation');

            return $this;
        };
    }

    public function setStandardBtmRelations()
    {
        return function () {
            $this->call('createNewBelongsToManyRelation')
                ->set('belongsToManyRelation.name', 'tags')
                ->set('belongsToManyRelation.displayColumn', 'name')
                ->call('addBelongsToManyRelation')
                ->call('createNewBelongsToManyRelation')

                ->set('belongsToManyRelation.name', 'categories')
                ->set('belongsToManyRelation.displayColumn', 'name')
                ->set('belongsToManyRelation.isMultiSelect', true)
                ->call('addBelongsToManyRelation');

            return $this;
        };
    }

    public function setStandardEagerLoadRelations()
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

    public function setStandardEagerLoadCountRelations()
    {
        return function () {
            $this->call('createNewWithCountRelation')
                ->set('withCountRelation.name', 'tags')
                ->call('addWithCountRelation')
                ->call('createNewWithCountRelation')
                ->set('withCountRelation.name', 'categories')
                ->set('withCountRelation.isSortable', true)
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
