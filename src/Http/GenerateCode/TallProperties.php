<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TallProperties 
{

    public $modelPath;
    public $primaryKey;
    public $componentName;

    public $otherModels;

    public $sortingFlag;
    public $defaultSortableColumn;

    public $searchingFlag;
    public $searchableColumns;

    public $paginationDropdownFlag;

    public $recordsPerPage;

    public $eagerLoadModels;

    public $eagerLoadCountModels;

    public $hideColumnsFlag;
    public $listingColumns;

    public $bulkActionFlag;
    public $bulkActionColumn;

    public $filterFlag;
    public $selfFilters;
    public $belongsToFilters;
    public $btmFilters;

    public $deleteFeatureFlag;
    public $editFeatureFlag;
    public $addFeatureFlag;

    public $flashMessageFlag;
    public $flashMessageText;

    public $btmRelations;
    public $belongsToRelations;

    public $advancedSettingsText;

    public $tableClasses;
    public $addFormFields;
    public $editFormFields;

    public function setModelPath($modelPath)
    {
        $this->modelPath = $modelPath;
    }

    public function getModelName($name = '')
    {
        if (empty($name)) {
            $name = $this->modelPath;
        }

        return Arr::last(Str::of($name)->explode('\\')->all());
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function setComponentName($componentName)
    {
        $this->componentName = $componentName;
    }

    public function getComponentName()
    {
        return $this->componentName;
    }

    public function getChildComponentName()
    {
        return $this->getComponentName().'-child';
    }

    public function setOtherModels($filters)
    {
        $this->otherModels = collect();
        foreach ($filters as $f) {
            if ($f['type'] == 'None') {
                continue;
            }
            $this->otherModels->push($f['modelPath']);
        }

        $this->otherModels->unique();
    }

    public function getOtherModels()
    {
        return $this->otherModels;
    }

    public function setSortingFlag($sortingFlag)
    {
        $this->sortingFlag = $sortingFlag;
    }
    
    public function isSortingEnabled()
    {
        return $this->sortingFlag;
    }

    public function setDefaultSortableColumn($defaultSortableColumn)
    {
        $this->defaultSortableColumn = $defaultSortableColumn;
    }

    public function getDefaultSortableColumn()
    {
        return $this->defaultSortableColumn;
    }

    public function setSearchingFlag($searchingFlag)
    {
        $this->searchingFlag = $searchingFlag;
    }
    
    public function isSearchingEnabled()
    {
        return $this->searchingFlag;
    }

    public function setSearchableColumns($columns)
    {
        $this->searchableColumns = collect();
        foreach($columns as $column)
        {
            $this->searchableColumns->push($column['column']);
        }
    }
    
    public function getSearchableColumns()
    {
        return $this->searchableColumns;
    }

    public function setPaginationDropdownFlag($paginationDropdownFlag)
    {
        $this->paginationDropdownFlag = $paginationDropdownFlag;
    }
    
    public function isPaginationDropdownEnabled()
    {
        return $this->paginationDropdownFlag;
    }

    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }
    
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    public function setEagerLoadModels($relations)
    {
        $this->eagerLoadModels = collect();
        foreach ($relations as $r) {
            $this->eagerLoadModels->push($r['relationName']);
        }
    }
    
    public function getEagerLoadModels()
    {
        return $this->eagerLoadModels;
    }

    public function setEagerLoadCountModels($relations)
    {
        $this->eagerLoadCountModels = collect();
        foreach ($relations as $r) {
            $this->eagerLoadCountModels->push($r['relationName']);
        }
    }
    
    public function getEagerLoadCountModels()
    {
        return $this->eagerLoadCountModels;
    }

    public function setHideColumnsFlag($hideColumnsFlag)
    {
        $this->hideColumnsFlag = $hideColumnsFlag;
    }
    
    public function isHideColumnsEnabled()
    {
        return $this->hideColumnsFlag;
    }

    public function setListingColumns($listingColumns)
    {
        $this->listingColumns = $listingColumns;
    }
    
    public function getListingColumns()
    {
        return $this->listingColumns;
    }

    public function setBulkActionFlag($bulkActionFlag)
    {
        $this->bulkActionFlag = $bulkActionFlag;
    }
    
    public function isBulkActionsEnabled()
    {
        return $this->bulkActionFlag;
    }

    public function setBulkActionColumn($bulkActionColumn)
    {
        $this->bulkActionColumn = $bulkActionColumn;
    }
    
    public function getBulkActionColumn()
    {
        return $this->bulkActionColumn;
    }

    public function setFilterFlag($filterFlag)
    {
        $this->filterFlag = $filterFlag;
    }

    public function isFilterEnabled()
    {
        return $this->filterFlag;
    }

    public function setFilters($filters)
    {
        $filters = collect($filters);
        $this->selfFilters = $filters->filter(function($item) {
            return $item['type'] == 'None';
        });
        $this->belongsToFilters = $filters->filter(function($item) {
            return $item['type'] == 'BelongsTo';
        });
        $this->btmFilters = $filters->filter(function($item) {
            return $item['type'] == 'BelongsToMany';
        });
    }

    public function setDeleteFeatureFlag($deleteFeaureFlag)
    {
        $this->deleteFeaureFlag = $deleteFeaureFlag;
    }

    public function isDeleteFeatureEnabled()
    {
        return $this->deleteFeaureFlag;
    }

    public function setAddFeatureFlag($addFeaureFlag)
    {
        $this->addFeaureFlag = $addFeaureFlag;
    }

    public function isAddFeatureEnabled()
    {
        return $this->addFeaureFlag;
    }

    public function setEditFeatureFlag($editFeaureFlag)
    {
        $this->editFeaureFlag = $editFeaureFlag;
    }

    public function isEditFeatureEnabled()
    {
        return $this->editFeaureFlag;
    }

    public function setFlashMessageFlag($flashMessageFlag)
    {
        $this->flashMessageFlag = $flashMessageFlag;
    }

    public function isFlashMessageEnabled()
    {
        return $this->flashMessageFlag;
    }

    public function setFlashMessageText($flashMessageText)
    {
        $this->flashMessageText = $flashMessageText;
    }

    public function getFlashMessageText($mode)
    {
        return $this->flashMessageText[$mode] ?? '';
    }

    public function setBtmRelations($btmRelations)
    {
        $this->btmRelations = collect($btmRelations);
    }

    public function getBtmRelations()
    {
        return $this->btmRelations;
    }

    public function getBtmAddFields()
    {
        return $this->getBtmRelations()->filter(function($item) {
            return $item['inAdd'];
        });
    }

    public function getBtmEditFields()
    {
        return $this->getBtmRelations()->filter(function($item) {
            return $item['inEdit'];
        });
    }

    public function setBelongsToRelations($belongsToRelations)
    {
        $this->belongsToRelations = collect($belongsToRelations);
    }

    public function getBelongsToRelations()
    {
        return $this->belongsToRelations;
    }

    public function getBelongsToAddFields()
    {
        return $this->getBelongsToRelations()->filter(function($item) {
            return $item['inAdd'];
        });
    }

    public function getBelongsToEditFields()
    {
        return $this->getBelongsToRelations()->filter(function($item) {
            return $item['inEdit'];
        });
    }

    public function setAdvancedSettingsText($advancedSettingsText)
    {
        $this->advancedSettingsText = $advancedSettingsText;
    }

    public function getAdvancedSettingsText($key)
    {
        return $this->advancedSettingsText[$key] ?? '';
    }

    public function setTableClasses($tableClasses)
    {
        $this->tableClasses = $tableClasses;
    }

    public function getTableClasses($key)
    {
        return $this->tableClasses[$key] ?? '';
    }

    public function needsActionColumn()
    {
        if ($this->isEditFeatureEnabled() || $this->isDeleteFeatureEnabled()) {
            return true;
        }

        return false;
    }

    public function setAddFormFields($addFormFields)
    {
        $this->addFormFields = $addFormFields;
    }

    public function getAddFormFields()
    {
        return $this->addFormFields;
    }

    public function setEditFormFields($editFormFields)
    {
        $this->editFormFields = $editFormFields;
    }

    public function getEditFormFields()
    {
        return $this->editFormFields;
    }

    public function getSelfFormFields()
    {
        $addFields = $this->getAddFormFields() ?? collect();
        $fields = $addFields->merge($this->getEditFormFields());
        return $fields->filter(function($field) {
            return $field['type'] == 'normal';
        })->unique('column');
    }

    public function getSelfAddFields()
    {
        return $this->getAddFormFields()->filter(function($item) {
            return $item['type'] == 'normal';
        });
    }
}