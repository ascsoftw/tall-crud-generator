<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

trait WithFeatures
{
    private function _hasAddAndEditFeaturesDisabled()
    {
        if (!$this->_isAddFeatureEnabled() && !$this->_isEditFeatureEnabled()) {
            return true;
        }
        return false;
    }

    private function _needsActionColumn()
    {
        if ($this->_isEditFeatureEnabled() || $this->_isDeleteFeatureEnabled()) {
            return true;
        }
        return false;
    }

    private function _needsPrimaryKeyInListing()
    {
        if ($this->primaryKeyProps['in_list']) {
            return true;
        }
        return false;
    }

    private function _isAddFeatureEnabled()
    {
        if ($this->componentProps['create_add_modal']) {
            return true;
        }
        return false;
    }

    private function _isEditFeatureEnabled()
    {
        if ($this->componentProps['create_edit_modal']) {
            return true;
        }
        return false;
    }

    private function _isDeleteFeatureEnabled()
    {
        if ($this->componentProps['create_delete_button']) {
            return true;
        }
        return false;
    }

    private function _isSortingEnabled()
    {
        if ($this->_isPrimaryKeySortable()) {
            return true;
        }

        $collection = collect($this->fields);
        return $collection->contains(function ($f) {
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) && $f['sortable']) {
                return true;
            }
            return false;
        });
    }

    private function _isSearchingEnabled()
    {
        $collection = collect($this->fields);
        return $collection->contains(function ($f) {
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['in_list']) && $f['searchable']) {
                return true;
            }
            return false;
        });
    }

    private function _isPrimaryKeySortable()
    {
        if ($this->_needsPrimaryKeyInListing() && $this->primaryKeyProps['sortable']) {
            return true;
        }
        return false;
    }

    private function _isColumnSortable($column)
    {
        $collection = collect($this->fields);
        $field = $collection->firstWhere('column', $column);
        if (empty($field)) {
            return false;
        }

        if ($field['sortable']) {
            return true;
        }

        return false;
    }

    private function _isFlashMessageEnabled()
    {
        if ($this->flashMessages['enable']) {
            return true;
        }
        return false;
    }
}
