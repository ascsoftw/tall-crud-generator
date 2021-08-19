<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

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
            'relation' => '',
            'isValid' => false,
            'column' => '',
            'columns' => [],
            'options' => [],
        ];
    }

    public function updatedFilterRelation()
    {
        $this->resetValidation('filter.*');
        $this->filter['isValid'] = false;
        $this->validateOnly('filter.relation', [
            'filter.relation' => 'required',
        ]);

        $this->fillFilterFields();
    }

    public function fillFilterFields()
    {
        $this->filter['isValid'] = true;
        $this->filter['columns'] = $this->modelProps['columns'];
        $this->filter['options'] = '{"": "Any", "0" : "No", "1": "Yes"}';
    }

    public function addFilter()
    {
        $this->resetValidation('filter.*');
        $this->validateOnly('filter.column', [
            'filter.column' => 'required',
        ]);
        $this->filters[] = [
            'relation' => $this->filter['relation'],
            'column' => $this->filter['column'],
            'options' => $this->filter['options'],
        ];
        $this->confirmingFilter = false;
    }

    public function deleteFilter($i)
    {
        unset($this->filters[$i]);
        $this->filters = array_values($this->filters);
    }
}
