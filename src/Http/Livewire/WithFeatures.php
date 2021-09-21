<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

trait WithFeatures
{
    public function hasAddAndEditFeaturesDisabled()
    {
        if (! $this->isAddFeatureEnabled() && ! $this->isEditFeatureEnabled()) {
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

    public function isHideColumnsEnabled()
    {
        return $this->advancedSettings['table_settings']['showHideColumns'];
    }

    public function isBulkActionsEnabled()
    {
        return $this->advancedSettings['table_settings']['bulkActions'] &&
            ! empty($this->advancedSettings['table_settings']['bulkActionColumn']);
    }
}
