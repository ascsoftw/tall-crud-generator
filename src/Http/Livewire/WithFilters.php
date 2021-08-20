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
        //check filter.type == BelongsTo here.
        $this->fillBelongsToFilterFields();
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

    public function addFilter()
    {
        $this->resetValidation('filter.*');
        $this->validateOnly('filter.column', [
            'filter.column' => 'required',
        ]);

        switch($this->filter['type']) {
            case 'None':
                $this->addNoRelationFilter();
                break;
            case 'BelongsTo':
                $this->addBelongsToFilter();
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

    public function deleteFilter($i)
    {
        unset($this->filters[$i]);
        $this->filters = array_values($this->filters);
    }
}
