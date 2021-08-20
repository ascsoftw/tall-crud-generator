<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Facades\Schema;

trait WithFilters
{

    public $confirmingFilter = false;
    public $filters = [];
    public $filter = [];

    public function createNewFilter()
    {
        $this->resetFilter();
        $this->resetValidation('filter.*');
        $this->confirmingFilter = true;
    }

    public function resetFilter()
    {
        $this->filter = [
            'type' => '',
            'isValid' => false,
            'relation' => '',
            'column' => '',
            'columns' => [],
            'options' => '',
            'modelPath' => '',
            'ownerKey' => '',
            'foreignKey' => '',
            'relatedKey' => '',
        ];
    }

    public function updatedFilterType()
    {
        $this->resetValidation('filter.*');
        $this->filter['isValid'] = false;
        $this->validateOnly('filter.type', [
            'filter.type' => 'required',
        ]);

        $this->fillFilterFields();
    }

    public function updatedFilterRelation()
    {
        $this->resetValidation('filter.*');
        $this->validateOnly('filter.relation', [
            'filter.relation' => 'required',
        ]);

        if($this->filter['type'] == 'BelongsTo') {
            $this->validateBelongsToRelation();
            $this->fillBelongsToFilterFields();
        }

        if($this->filter['type'] == 'BelongsToMany') {
            $this->validateBelongsToManyRelation();
            $this->fillBelongsToManyFilterFields();
        }
    }

    public function updatedFilterColumn()
    {
        if($this->filter['type'] != 'None') {
            return true;
        }

        $this->validateColumnForNoRelation();
    }

    public function validateColumnForNoRelation()
    {

        foreach ($this->filters as $f) {
            if($f['type'] != 'None') {
                continue;
            }
            if ($f['column'] == $this->filter['column']) {
                $this->addError('filter.column', 'Filter Already Defined.');

                return false;
            }
        }

        return true;
    }

    public function validateBelongsToRelation()
    {
        foreach ($this->filters as $f) {
            if($f['type'] != 'BelongsTo') {
                continue;
            }
            if ($f['relation'] == $this->filter['relation']) {
                $this->addError('filter.relation', 'Filter Already Defined.');

                return false;
            }
        }

        return true;
    }

    public function validateBelongsToManyRelation()
    {
        foreach ($this->filters as $f) {
            if($f['type'] != 'BelongsToMany') {
                continue;
            }
            if ($f['relation'] == $this->filter['relation']) {
                $this->addError('filter.relation', 'Filter Already Defined.');

                return false;
            }
        }

        return true;
    }

    public function fillFilterFields()
    {
        $this->filter['isValid'] = true;
        if($this->filter['type'] == 'None') {
            $this->filter['columns'] = $this->modelProps['columns'];
            $this->filter['options'] = '{"": "Any", "0" : "No", "1": "Yes"}';
        }
    }

    public function fillBelongsToFilterFields()
    {
        $model = new $this->modelPath();
        $relationName = $this->filter['relation'];
        $relation = $model->{$relationName}();
        $this->filter['modelPath'] = get_class($relation->getRelated());
        $this->filter['ownerKey'] = $relation->getOwnerKeyName();
        $this->filter['foreignKey'] = $relation->getForeignKeyName();
        $this->filter['columns'] = $this->getColumns(Schema::getColumnListing($relation->getRelated()->getTable()), null);
    }

    public function fillBelongsToManyFilterFields()
    {
        $model = new $this->modelPath();
        $relationName = $this->filter['relation'];
        $relation = $model->{$relationName}();
        $this->filter['modelPath'] = get_class($relation->getRelated());
        $this->filter['relatedKey'] = $relation->getRelatedKeyName();
        $this->filter['columns'] = $this->getColumns(Schema::getColumnListing($relation->getRelated()->getTable()), null);
    }

    public function addFilter()
    {
        $this->resetValidation('filter.*');
        $this->validateOnly('filter.column', [
            'filter.column' => 'required',
        ]);

        switch($this->filter['type']) {
            case 'None':
                if (! $this->validateColumnForNoRelation()) {
                    return;
                }
                $this->addNoRelationFilter();
                break;
            case 'BelongsTo':
                if (! $this->validateBelongsToRelation()) {
                    return;
                }
                $this->addBelongsToFilter();
                break;
            case 'BelongsToMany':
                if (! $this->validateBelongsToManyRelation()) {
                    return;
                }
                $this->addBelongsToManyFilter();
                break;
        }
        $this->confirmingFilter = false;
    }

    public function addNoRelationFilter()
    {

        $this->filters[] = [
            'type' => $this->filter['type'],
            'column' => $this->filter['column'],
            'options' => $this->filter['options'],
        ];
    }

    public function addBelongsToFilter()
    {
        $this->filters[] = [
            'type' => $this->filter['type'],
            'relation' => $this->filter['relation'],
            'column' => $this->filter['column'],
            'modelPath' => $this->filter['modelPath'],
            'ownerKey' => $this->filter['ownerKey'],
            'foreignKey' => $this->filter['foreignKey'],
        ];
    }

    public function addBelongsToManyFilter()
    {
        $this->filters[] = [
            'type' => $this->filter['type'],
            'relation' => $this->filter['relation'],
            'column' => $this->filter['column'],
            'modelPath' => $this->filter['modelPath'],
            'relatedKey' => $this->filter['relatedKey'],
        ];
    }

    public function deleteFilter($i)
    {
        unset($this->filters[$i]);
        $this->filters = array_values($this->filters);
    }
}
