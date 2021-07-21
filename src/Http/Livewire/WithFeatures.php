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
        if ($this->primaryKeyProps['inList']) {
            return true;
        }
        return false;
    }

    private function _isAddFeatureEnabled()
    {
        if ($this->componentProps['createAddModal']) {
            return true;
        }
        return false;
    }

    private function _isEditFeatureEnabled()
    {
        if ($this->componentProps['createEditModal']) {
            return true;
        }
        return false;
    }

    private function _isDeleteFeatureEnabled()
    {
        if ($this->componentProps['createDeleteButton']) {
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
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['inList']) && $f['sortable']) {
                return true;
            }
            return false;
        });
    }

    private function _isSearchingEnabled()
    {
        $collection = collect($this->fields);
        return $collection->contains(function ($f) {
            if (($this->_hasAddAndEditFeaturesDisabled() || $f['inList']) && $f['searchable']) {
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
        return $this->flashMessages['enable'];
    }

    private function _isPaginationDropdownEnabled()
    {
        return $this->advancedSettings['table_settings']['showPaginationDropdown'];
    }

    private function _isBtmEnabled()
    {
        return count($this->belongsToManyRelations) > 0 ;
    }

    private function _isBtmAddEnabled()
    {
        $collection = collect($this->belongsToManyRelations);
        $c = $collection->firstWhere('inAdd', true);
        if(is_null($c)) {
            return false;
        }
        return true;
    }

    private function _isBtmEditEnabled()
    {
        $collection = collect($this->belongsToManyRelations);
        $c = $collection->firstWhere('inEdit', true);
        if(is_null($c)) {
            return false;
        }
        return true;
    }

    private function _isBelongsToEnabled()
    {
        return count($this->belongsToRelations) > 0 ;
    }

    private function _isBelongsToAddEnabled()
    {
        $collection = collect($this->belongsToRelations);
        $c = $collection->firstWhere('inAdd', true);
        if(is_null($c)) {
            return false;
        }
        return true;
    }

    private function _isBelongsToEditEnabled()
    {
        $collection = collect($this->belongsToRelations);
        $c = $collection->firstWhere('inEdit', true);
        if(is_null($c)) {
            return false;
        }
        return true;
    }
}