<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait WithHelpers
{
    private function _getModelName()
    {
        return Arr::last(Str::of($this->modelPath)->explode('\\')->all());
    }

    private function _newLines($count = 1, $indent = 0)
    {
        return str_repeat("\n" . $this->_indent($indent), $count);
    }

    private function _spaces($count = 1)
    {
        return str_repeat(" ", $count);
    }

    private function _indent($step = 1)
    {
        return $this->_spaces($step * 4);
    }

    private function _getColumns($columns, $primaryKey)
    {
        $collection = collect($columns);

        $filtered = $collection->reject(function ($value) use ($primaryKey) {
            return $value == $primaryKey;
        });

        return $filtered->all();
    }

    private function _getDefaultSortableColumn()
    {
        if ($this->_isPrimaryKeySortable()) {
            return $this->modelProps['primary_key'];
        }

        $collection = collect($this->fields);
        $field =  $collection->first(function ($f) {
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) && $f['searchable']) {
                return true;
            }
            return false;
        });

        return $field['column'];
    }

    private function _getSearchableColumns()
    {
        $columns = [];
        foreach ($this->fields as $f) {
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) && $f['searchable']) {
                $columns[] = $f;
            }
        }
        return $columns;
    }

    private function _validateEmptyColumns()
    {
        $collection = collect($this->fields);

        $filtered = $collection->reject(function ($field) {
            return empty($field['column']);
        });

        return count($this->fields) == count($filtered->all());
    }

    private function _validateUniqueFields()
    {
        $collection = collect($this->fields);
        $filtered = $collection->duplicates('column')->all();
        return 0 == count($filtered);
    }

    private function _validateEachRow()
    {
        if ($this->_hasAddAndEditFeaturesDisabled()) {
            return true;
        }
        foreach ($this->fields as $f) {
            if (!($f['in_list'] ||
                ($this->_isAddFeatureEnabled() && $f['in_add']) ||
                ($this->_isEditFeatureEnabled()  && $f['in_edit']))) {
                $this->addError('fields', $f['column'] . ' Column should be selected to display in at least 1 view.');
                return false;
            }
        }
        return true;
    }

    private function _validateDisplayColumn()
    {
        if ($this->_hasAddAndEditFeaturesDisabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['in_list']);
        });

        return 0 != count($filtered->all());
    }

    private function _validateCreateColumn()
    {
        if (!$this->_isAddFeatureEnabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['in_add']);
        });

        return 0 != count($filtered->all());
    }

    private function _validateEditColumn()
    {
        if (!$this->_isEditFeatureEnabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['in_edit']);
        });

        return 0 != count($filtered->all());
    }

    private function _getChildComponentName()
    {
        return $this->componentName . '-child';
    }

    private function _getFormFields($addForm = true, $editForm = true)
    {
        if ($this->_hasAddAndEditFeaturesDisabled()) {
            return [];
        }

        $columns = [];
        foreach ($this->fields as $f) {
            if (
                ($addForm && $f['in_add']) ||
                ($editForm && $f['in_edit'])
            ) {
                $columns[] = $f;
            }
        }
        return $columns;
    }

    private function _getLabel($label = '', $column = '')
    {
        if (!empty($label)) {
            return $label;
        }

        return Str::studly(Str::replace('_', ' ', $column));
    }
}
