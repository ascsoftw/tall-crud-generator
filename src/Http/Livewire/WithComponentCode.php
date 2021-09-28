<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Illuminate\Support\Facades\App;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\TallProperties;

trait WithComponentCode
{
    public function generateComponentCode()
    {

        $this->tallProperties = App::make(TallProperties::class);
        //
        $this->tallProperties->setPrimaryKey($this->getPrimaryKey());
        $this->tallProperties->setModelPath($this->modelPath);
        $this->tallProperties->setComponentName($this->getComponentName());
        $this->tallProperties->setDeleteFeatureFlag($this->isDeleteFeatureEnabled());
        $this->tallProperties->setAddFeatureFlag($this->isAddFeatureEnabled());
        $this->tallProperties->setEditFeatureFlag($this->isEditFeatureEnabled());

        //Sorting
        $this->tallProperties->setSortingFlag($this->isSortingEnabled());
        if ($this->tallProperties->isSortingEnabled()) {
            $this->tallProperties->setDefaultSortableColumn($this->getDefaultSortableColumn());
        }
        //Searching
        $this->tallProperties->setSearchingFlag($this->isSearchingEnabled());
        if ($this->tallProperties->isSearchingEnabled()) {
            $this->tallProperties->setSearchableColumns($this->getSearchableColumns());
        }
        //Pagination Dropdown
        $this->tallProperties->setPaginationDropdownFlag($this->advancedSettings['table_settings']['showPaginationDropdown']);
        //Records Per Page
        $this->tallProperties->setRecordsPerPage($this->advancedSettings['table_settings']['recordsPerPage']);
        //Eager Load Models
        $this->tallProperties->setEagerLoadModels($this->withRelations);
        //Eager Load Count Models
        $this->tallProperties->setEagerLoadCountModels($this->withCountRelations);
        //Hide Column
        $this->tallProperties->setHideColumnsFlag($this->advancedSettings['table_settings']['showHideColumns']);
        //Bulk Actions
        $this->tallProperties->setBulkActionFlag($this->isBulkActionsEnabled());
        if ($this->tallProperties->isBulkActionsEnabled()) {
            $this->tallProperties->setBulkActionColumn($this->advancedSettings['table_settings']['bulkActionColumn']);
        }
        //Filters
        $this->tallProperties->setFilterFlag(count($this->filters) > 0);
        if ($this->tallProperties->isFilterEnabled()) {
            $this->tallProperties->setFilters($this->filters);
        }
        //Other Models
        $this->tallProperties->setOtherModels($this->filters);

        $this->tallProperties->setFlashMessageFlag($this->flashMessages['enable']);
        if ($this->tallProperties->isFlashMessageEnabled()) {
            $this->tallProperties->setFlashMessageText($this->flashMessages['text']);
        }
        $this->tallProperties->setBtmRelations($this->belongsToManyRelations);
        $this->tallProperties->setBelongsToRelations($this->belongsToRelations);

        $this->tallProperties->setAdvancedSettingsText($this->advancedSettings['text']);
        $this->tallProperties->setTableClasses($this->advancedSettings['table_settings']['classes']);

        $this->tallProperties->setListingColumns($this->getListingColumns());
        if ($this->tallProperties->isAddFeatureEnabled()) {
            $this->tallProperties->setAddFormFields($this->getSortedFormFields(true));
        }
        if ($this->tallProperties->isEditFeatureEnabled()) {
            $this->tallProperties->setEditFormFields($this->getSortedFormFields(false));
        }

        $this->componentCode = App::make(ComponentCode::class);

        $code = [];
        $code['sort'] = $this->componentCode->getSortCode();
        $code['search'] = $this->componentCode->getSearchCode();
        $code['pagination_dropdown'] = $this->componentCode->getPaginationDropdownCode();
        $code['pagination'] = $this->componentCode->getPaginationCode();
        $code['with_query'] = $this->componentCode->getWithQueryCode();
        $code['with_count_query'] = $this->componentCode->getWithCountQueryCode();
        $code['hide_columns'] = $this->componentCode->getHideColumnsCode();
        $code['bulk_actions'] = $this->componentCode->getBulkActionsCode();
        $code['filter'] = $this->componentCode->getFilterCode();
        $code['other_models'] = $this->componentCode->getOtherModelsCode();

        $this->childComponentCode = App::make(ChildComponentCode::class);
        $code['child_delete'] = $this->childComponentCode->getDeleteCode();
        $code['child_add'] = $this->childComponentCode->getAddCode();
        $code['child_edit'] = $this->childComponentCode->getEditCode();
        $code['child_listeners'] = $this->childComponentCode->getChildListenersCode();
        $code['child_item'] = $this->childComponentCode->getChildItemCode();
        $code['child_rules'] = $this->childComponentCode->getChildRulesCode();
        $code['child_validation_attributes'] = $this->childComponentCode->getChildValidationAttributes();
        $code['child_other_models'] = $this->childComponentCode->getChildOtherModelsCode();
        $code['child_vars'] = $this->childComponentCode->getRelationVars();

        return $code;
    }
}
