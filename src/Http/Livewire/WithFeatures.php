<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

trait WithFeatures
{
    public function hasAddAndEditFeaturesDisabled()
    {
        if (!$this->isAddFeatureEnabled() && !$this->isEditFeatureEnabled()) {
            return true;
        }
        return false;
    }

    public function needsActionColumn()
    {
        if ($this->isEditFeatureEnabled() || $this->isDeleteFeatureEnabled()) {
            return true;
        }
        return false;
    }

    public function needsPrimaryKeyInListing()
    {
        if ($this->primaryKeyProps['inList']) {
            return true;
        }
        return false;
    }

    public function isAddFeatureEnabled()
    {
        if ($this->componentProps['createAddModal']) {
            return true;
        }
        return false;
    }

    public function isEditFeatureEnabled()
    {
        if ($this->componentProps['createEditModal']) {
            return true;
        }
        return false;
    }

    public function isDeleteFeatureEnabled()
    {
        if ($this->componentProps['createDeleteButton']) {
            return true;
        }
        return false;
    }

    public function isSortingEnabled()
    {
        if ($this->isPrimaryKeySortable()) {
            return true;
        }

        $collection = collect($this->fields);
        return $collection->contains(function ($f) {
            if (($this->hasAddAndEditFeaturesDisabled() || $f['inList']) && $f['sortable']) {
                return true;
            }
            return false;
        });
    }

    public function isSearchingEnabled()
    {
        $collection = collect($this->fields);
        return $collection->contains(function ($f) {
            if (($this->hasAddAndEditFeaturesDisabled() || $f['inList']) && $f['searchable']) {
                return true;
            }
            return false;
        });
    }

    public function isPrimaryKeySortable()
    {
        if ($this->needsPrimaryKeyInListing() && $this->primaryKeyProps['sortable']) {
            return true;
        }
        return false;
    }

    public function isColumnSortable($column)
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

    public function isFlashMessageEnabled()
    {
        return $this->flashMessages['enable'];
    }

    public function isPaginationDropdownEnabled()
    {
        return $this->advancedSettings['table_settings']['showPaginationDropdown'];
    }

    public function isBtmEnabled()
    {
        return count($this->belongsToManyRelations) > 0;
    }

    public function isBtmAddEnabled()
    {
        $collection = collect($this->belongsToManyRelations);
        $c = $collection->firstWhere('inAdd', true);
        if (is_null($c)) {
            return false;
        }
        return true;
    }

    public function isBtmEditEnabled()
    {
        $collection = collect($this->belongsToManyRelations);
        $c = $collection->firstWhere('inEdit', true);
        if (is_null($c)) {
            return false;
        }
        return true;
    }

    public function isBelongsToEnabled()
    {
        return count($this->belongsToRelations) > 0;
    }

    public function isBelongsToAddEnabled()
    {
        $collection = collect($this->belongsToRelations);
        $c = $collection->firstWhere('inAdd', true);
        if (is_null($c)) {
            return false;
        }
        return true;
    }

    public function isBelongsToEditEnabled()
    {
        $collection = collect($this->belongsToRelations);
        $c = $collection->firstWhere('inEdit', true);
        if (is_null($c)) {
            return false;
        }
        return true;
    }
}
