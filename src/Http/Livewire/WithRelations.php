<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;

trait WithRelations
{

    public $allRelations = [];

    public $confirmingBelongsToMany = false;
    public $belongsToManyRelation = [];
    public $belongsToManyRelations = [];

    public $confirmingBelongsTo = false;
    public $belongsToRelation = [];
    public $belongsToRelations = [];

    public $confirmingWith = false;
    public $withRelation = [];
    public $withRelations = [];

    public $confirmingWithCount = false;
    public $withCountRelation = [];
    public $withCountRelations = [];

    public function createNewBelongsToManyRelation()
    {
        $this->resetBelongsToManyRelation();
        $this->resetValidation('belongsToManyRelation.*');
        $this->confirmingBelongsToMany = true;
    }

    public function resetBelongsToManyRelation()
    {
        $this->belongsToManyRelation = [
            'name' => '',
            'is_valid' => false,
            'relatedKey' => '',
            'modelPath' => '',
            'columns' => [],
            'displayColumn' => '',
            'in_add' => true,
            'in_edit' => true,
        ];
    }

    public function updatedBelongsToManyRelationName()
    {
        $this->resetValidation('belongsToManyRelation.*');
        $this->belongsToManyRelation['is_valid'] = false;

        if (!$this->_isValidBelongsToManyName()) {
            return;
        }

        $this->_fillBelongsToManyFields();
    }

    private function _isValidBelongsToManyName()
    {
        if (empty($this->belongsToManyRelation['name'])) {
            $this->addError('belongsToManyRelation.name', 'Please select a Relation');
            return false;
        }

        foreach ($this->belongsToManyRelations as $k) {
            if ($k['relationName'] == $this->belongsToManyRelation['name']) {
                $this->addError('belongsToManyRelation.name', 'Relation Already Defined.');
                return false;
            }
        }

        return true;
    }

    private function _fillBelongsToManyFields()
    {
        $model = new $this->modelPath();
        $relationName = $this->belongsToManyRelation['name'];
        $relation = $model->{$relationName}();
        $this->belongsToManyRelation['is_valid'] = true;
        $this->belongsToManyRelation['relatedKey'] = $relation->getRelatedKeyName();
        $this->belongsToManyRelation['modelPath'] = get_class($relation->getRelated());
        $this->belongsToManyRelation['columns'] = $this->_getColumns(Schema::getColumnListing($relation->getRelated()->getTable()), null);
    }

    public function addBelongsToManyRelation()
    {
        $this->resetValidation('belongsToManyRelation.*');
        if (!$this->_isValidBelongsToManyName()) {
            return;
        }
        $this->validateOnly('belongsToManyRelation.displayColumn', [
            'belongsToManyRelation.displayColumn' => 'required',
        ]);
        $this->belongsToManyRelations[] = [
            'relationName' => $this->belongsToManyRelation['name'],
            'relatedKey' => $this->belongsToManyRelation['relatedKey'],
            'modelPath' => $this->belongsToManyRelation['modelPath'],
            'displayColumn' => $this->belongsToManyRelation['displayColumn'],
            'in_add' => $this->belongsToManyRelation['in_add'],
            'in_edit' => $this->belongsToManyRelation['in_edit'],
        ];
        $this->confirmingBelongsToMany = false;
        $this->resetRelationsForm();
    }

    public function deleteBelongsToManyRelation($i)
    {
        unset($this->belongsToManyRelations[$i]);
        $this->belongsToManyRelations = array_values($this->belongsToManyRelations);
    }

    public function createNewBelongsToRelation()
    {
        $this->resetBelongsToRelation();
        $this->resetValidation('belongsToRelation.*');
        $this->confirmingBelongsTo = true;
    }

    public function resetBelongsToRelation()
    {
        $this->belongsToRelation = [
            'name' => '',
            'is_valid' => false,
            'ownerKey' => '',
            'modelPath' => '',
            'columns' => [],
            'displayColumn' => '',
            'column' => '',
            'in_add' => true,
            'in_edit' => true,
        ];
    }

    public function updatedBelongsToRelationName()
    {
        $this->resetValidation('belongsToRelation.*');
        $this->belongsToRelation['is_valid'] = false;

        if (!$this->_isValidBelongsToName()) {
            return;
        }

        $this->_fillBelongsToFields();
    }

    private function _isValidBelongsToName()
    {
        if (empty($this->belongsToRelation['name'])) {
            $this->addError('belongsToRelation.name', 'Please select a Relation');
            return false;
        }

        foreach ($this->belongsToRelations as $k) {
            if ($k['relationName'] == $this->belongsToRelation['name']) {
                $this->addError('belongsToRelation.name', 'Relation Already Defined.');
                return false;
            }
        }

        return true;
    }

    private function _fillBelongsToFields()
    {
        $model = new $this->modelPath();
        $relationName = $this->belongsToRelation['name'];
        $relation = $model->{$relationName}();
        $this->belongsToRelation['is_valid'] = true;
        $this->belongsToRelation['ownerKey'] = $relation->getOwnerKeyName();
        $this->belongsToRelation['modelPath'] = get_class($relation->getRelated());
        $this->belongsToRelation['columns'] = $this->_getColumns(Schema::getColumnListing($relation->getRelated()->getTable()), null);
    }

    public function addBelongsToRelation()
    {
        $this->resetValidation('belongsToRelation.*');
        if (!$this->_isValidBelongsToName()) {
            return;
        }
        $this->validateOnly('belongsToRelation.displayColumn', [
            'belongsToRelation.displayColumn' => 'required',
        ]);
        $this->validateOnly('belongsToRelation.column', [
            'belongsToRelation.column' => 'required',
        ]);

        $this->belongsToRelations[] = [
            'relationName' => $this->belongsToRelation['name'],
            'ownerKey' => $this->belongsToRelation['ownerKey'],
            'modelPath' => $this->belongsToRelation['modelPath'],
            'displayColumn' => $this->belongsToRelation['displayColumn'],
            'column' => $this->belongsToRelation['column'],
            'in_add' => $this->belongsToRelation['in_add'],
            'in_edit' => $this->belongsToRelation['in_edit'],
        ];
        $this->confirmingBelongsTo = false;
        $this->resetRelationsForm();
    }

    public function deleteBelongsToRelation($i)
    {
        unset($this->belongsToRelations[$i]);
        $this->belongsToRelations = array_values($this->belongsToRelations);
    }

    public function createNewWithRelation()
    {
        $this->resetWithRelation();
        $this->resetValidation('withRelation.*');
        $this->confirmingWith = true;
    }

    public function resetWithRelation()
    {
        $this->withRelation = [
            'name' => '',
            'is_valid' => false,
            'modelPath' => '',
            'columns' => [],
            'displayColumn' => '',
        ];
    }

    public function updatedWithRelationName()
    {
        $this->resetValidation('withRelation.*');
        $this->withRelation['is_valid'] = false;

        if (!$this->_isValidWithName()) {
            return;
        }

        $this->_fillWithFields();
    }

    private function _isValidWithName()
    {
        if (empty($this->withRelation['name'])) {
            $this->addError('withRelation.name', 'Please select a Relation');
            return false;
        }

        foreach ($this->withRelations as $k) {
            if ($k['relationName'] == $this->withRelation['name']) {
                $this->addError('withRelation.name', 'Relation Already Defined.');
                return false;
            }
        }

        return true;
    }

    private function _fillWithFields()
    {
        $model = new $this->modelPath();
        $relationName = $this->withRelation['name'];
        $relation = $model->{$relationName}();
        $this->withRelation['is_valid'] = true;
        $this->withRelation['modelPath'] = get_class($relation->getRelated());
        $this->withRelation['columns'] = $this->_getColumns(Schema::getColumnListing($relation->getRelated()->getTable()), null);
    }

    public function addWithRelation()
    {
        $this->resetValidation('withRelation.*');
        if (!$this->_isValidWithName()) {
            return;
        }
        $this->validateOnly('withRelation.displayColumn', [
            'withRelation.displayColumn' => 'required',
        ]);
        $this->withRelations[] = [
            'relationName' => $this->withRelation['name'],
            'modelPath' => $this->withRelation['modelPath'],
            'displayColumn' => $this->withRelation['displayColumn'],
        ];
        $this->confirmingWith = false;
        $this->resetRelationsForm();
    }

    public function deleteWithRelation($i)
    {
        unset($this->withRelations[$i]);
        $this->withRelations = array_values($this->withRelations);
    }

    public function createNewWithCountRelation()
    {
        $this->resetWithCountRelation();
        $this->resetValidation('withCountRelation.*');
        $this->confirmingWithCount = true;
    }

    public function resetWithCountRelation()
    {
        $this->withCountRelation = [
            'name' => '',
            'is_valid' => false,
            'is_sortable' => false,
        ];
    }

    public function updatedWithCountRelationName()
    {
        $this->resetValidation('withCountRelation.*');
        $this->withCountRelation['is_valid'] = false;

        if (!$this->_isValidWithCountName()) {
            return;
        }

        $this->withCountRelation['is_valid'] = true;
    }

    private function _isValidWithCountName()
    {
        if (empty($this->withCountRelation['name'])) {
            $this->addError('withCountRelation.name', 'Please select a Relation');
            return false;
        }

        foreach ($this->withCountRelations as $k) {
            if ($k['relationName'] == $this->withCountRelation['name']) {
                $this->addError('withCountRelation.name', 'Relation Already Defined.');
                return false;
            }
        }

        return true;
    }

    public function addWithCountRelation()
    {
        $this->resetValidation('withCountRelation.*');
        if (!$this->_isValidWithCountName()) {
            return;
        }

        $this->withCountRelations[] = [
            'relationName' => $this->withCountRelation['name'],
            'is_sortable' => $this->withCountRelation['is_sortable'],
        ];
        $this->confirmingWithCount = false;
        $this->resetRelationsForm();
    }

    public function deleteWithCountRelation($i)
    {
        unset($this->withCountRelations[$i]);
        $this->withCountRelations = array_values($this->withCountRelations);
    }

    public function resetRelationsForm()
    {
        $this->resetBelongsToManyRelation();
        $this->resetBelongsToRelation();
        $this->resetWithRelation();
        $this->resetWithCountRelation();
    }

    public function getAllRelations()
    {

        $this->resetRelationsForm();

        $this->allRelations = [];
        $reflectionClass = new \ReflectionClass($this->modelPath);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $model = new $this->modelPath();
        foreach ($methods as $m) {
            if ($m->class != $this->modelPath) {
                continue;
            }
            if ($m->isStatic()) {
                continue;
            }

            if (count($m->getParameters())) {
                continue;
            }

            try {
                $methodName = $m->getName();
                $methodReturn = $model->{$methodName}();

                if (!$methodReturn instanceof Relation) {
                    return;
                }

                if ($methodReturn instanceof BelongsToMany) {
                    $this->allRelations['belongsToMany'][] = ['name' => $methodName];
                }

                if ($methodReturn instanceof BelongsTo) {
                    $this->allRelations['belongsTo'][] = ['name' => $methodName];
                }
            } catch (Exception $ignore) {
                //some issue running the $methodName
            }
        }
    }
}
