<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Illuminate\Support\Facades\App;

trait WithComponentCode
{
    public function generateComponentCode()
    {

        $this->tallProperties = App::make(TallProperties::class);
        //
        $this->tallProperties->setPrimaryKey($this->getPrimaryKey());
        $this->tallProperties->setModelPath($this->modelPath);
        $this->tallProperties->setComponentName($this->getComponentName());
        //Sorting
        $this->tallProperties->setSortingFlag($this->isSortingEnabled());
        if ($this->tallProperties->getSortingFlag()) {
            $this->tallProperties->setDefaultSortableColumn($this->getDefaultSortableColumn());
        }
        //Searching
        $this->tallProperties->setSearchingFlag($this->isSearchingEnabled());
        if ($this->tallProperties->getSearchingFlag()) {
            $this->tallProperties->setSearchableColumns($this->getSearchableColumns());
        }
        //Pagination Dropdown
        $this->tallProperties->setPaginationDropdownFlag($this->isPaginationDropdownEnabled());
        //Records Per Page
        $this->tallProperties->setRecordsPerPage($this->advancedSettings['table_settings']['recordsPerPage']);
        //Eager Load Models
        $this->tallProperties->setEagerLoadModels($this->withRelations);
        //Eager Load Count Models
        $this->tallProperties->setEagerLoadCountModels($this->withCountRelations);
        //Hide Column
        $this->tallProperties->setHideColumnsFlag($this->isHideColumnsEnabled());
        if ($this->tallProperties->getHideColumnsFlag()) {
            $this->tallProperties->setListingColumns($this->getAllListingColumns());
        }
        //Bulk Actions
        $this->tallProperties->setBulkActionFlag($this->isBulkActionsEnabled());
        if ($this->tallProperties->getBulkActionFlag()) {
            $this->tallProperties->setBulkActionColumn($this->advancedSettings['table_settings']['bulkActionColumn']);
        }
        //Filters
        $this->tallProperties->setFilterFlag($this->isFilterEnabled());
        if ($this->tallProperties->getFilterFlag()) {
            $this->tallProperties->setFilters($this->filters);
        }
        //Other Models
        $this->tallProperties->setOtherModels($this->filters);

        $this->tallProperties->setDeleteFeatureFlag($this->isDeleteFeatureEnabled());
        $this->tallProperties->setAddFeatureFlag($this->isAddFeatureEnabled());
        $this->tallProperties->setEditFeatureFlag($this->isEditFeatureEnabled());
        $this->tallProperties->setFlashMessageFlag($this->isFlashMessageEnabled());
        if ($this->tallProperties->getFlashMessageFlag()) {
            $this->tallProperties->setFlashMessageText($this->flashMessages['text']);
        }
        $this->tallProperties->setSelfFormFields($this->getNormalFormFields());
        $this->tallProperties->setBtmRelations($this->belongsToManyRelations);
        $this->tallProperties->setBelongsToRelations($this->belongsToRelations);

        $this->tallProperties->setAdvancedSettingsText($this->advancedSettings['text']);

        $this->componentCode = new ComponentCode($this->tallProperties);

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

        $this->childComponentCode = new ChildComponentCode($this->tallProperties);
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
