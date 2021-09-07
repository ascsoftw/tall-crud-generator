<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Illuminate\Support\Str;
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
        // $code['child_rules'] = $this->childComponentCode->getChildRulesCode();
        $code['child_rules'] = $this->generateChildRules();
        $code['child_validation_attributes'] = $this->generateChildValidationAttributes();
        $code['child_other_models'] = $this->generateChildOtherModelsCode();
        $code['child_vars'] = $this->getRelationVars();

        return $code;
    }

    public function generateChildRules()
    {
        $fields = $this->getNormalFormFields();
        $rules = collect();

        foreach ($fields as $field) {
            $rules->push(
                $this->getChildFieldCode(
                    $field['column'],
                    Str::of($field['attributes']['rules'])->explode(',')->filter()->join('|')
                )
            );
        }

        if ($this->isBelongsToEnabled()) {
            $rules->push($this->getRulesForBelongsToFields());
        }

        return str_replace('##RULES##', $rules->prependAndJoin($this->newLines(1, 2)), $this->getChildRulesTemplate());
    }

    public function generateChildValidationAttributes()
    {
        $fields = $this->getNormalFormFields();
        $attributes = collect();
        foreach ($fields as $field) {
            $attributes->push(
                $this->getChildFieldCode(
                    $field['column'],
                    $this->getLabel($field['label'], $field['column'])
                )
            );
        }

        if ($this->isBelongsToEnabled()) {
            $attributes->push($this->getAttributesForBelongsToFields());
        }

        return str_replace(
            '##ATTRIBUTES##',
            $attributes->prependAndJoin($this->newLines(1, 2)),
            $this->getChildValidationAttributesTemplate()
        );
    }

    public function generateChildOtherModelsCode()
    {
        return $this->generateBtmModelsCode() . $this->generateBelongstoModelsCode();
    }

    public function generateBtmModelsCode()
    {
        if (!$this->isBtmEnabled()) {
            return '';
        }

        $modelsCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            $modelsCode->push($this->getOtherModelCode($r['modelPath']));
        }

        return $modelsCode->unique()->implode('');
    }

    public function generateBelongstoModelsCode()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $modelsCode = collect();
        foreach ($this->belongsToRelations as $r) {
            $modelsCode->push($this->getOtherModelCode($r['modelPath']));
        }

        return $modelsCode->unique()->implode('');
    }

    public function getRelationVars()
    {
        return $this->getBtmVars() . $this->getBelongsToVars();
    }

    public function getBtmVars()
    {
        if (!$this->isBtmEnabled()) {
            return '';
        }

        $vars = collect();
        foreach ($this->belongsToManyRelations as $r) {
            $vars->push($this->getArrayCode($r['relationName']));
            $vars->push(
                $this->getArrayCode(
                    $this->getBtmFieldName($r['relationName'])
                )
            );
        }

        return $vars->prependAndJoin($this->newLines());
    }

    public function getBelongsToVars()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $vars = collect();
        foreach ($this->belongsToRelations as $r) {
            $vars->push(
                $this->getArrayCode(
                    $this->getBelongsToVarName($r['relationName'])
                )
            );
        }

        return $vars->prependAndJoin($this->newLines());
    }

    public function getRulesForBelongsToFields()
    {
        $rules = collect();
        foreach ($this->belongsToRelations as $r) {
            $rules->push(
                $this->getChildFieldCode($r['foreignKey'], 'required')
            );
        }

        return $rules->join($this->newLines(1, 2));
    }

    public function getAttributesForBelongsToFields()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $attributes = collect();
        foreach ($this->belongsToRelations as $r) {
            $attributes->push(
                $this->getChildFieldCode(
                    $r['foreignKey'],
                    Str::ucfirst($r['relationName'])
                )
            );
        }

        return $attributes->join($this->newLines(1, 2));
    }


    public function getOtherModelCode($modelPath)
    {
        return str_replace(
            '##MODEL##',
            $modelPath,
            $this->getOtherModelTemplate()
        );
    }

    public function getArrayCode($name, $type = 'array')
    {
        return str_replace(
            [
                '##NAME##',
                '##TYPE##',
            ],
            [
                $name,
                $type,
            ],
            $this->getArrayTemplate()
        );
    }

    public function getFlashCode($message)
    {
        if (empty($message)) {
            return '';
        }

        return str_replace(
            '##MESSAGE##',
            $message,
            $this->getFlashTemplate()
        );
    }

    public function getChildFieldCode($columnName, $value)
    {
        return str_replace(
            [
                '##COLUMN_NAME##',
                '##VALUE##',
            ],
            [
                $columnName,
                $value,
            ],
            $this->getChildFieldTemplate()
        );
    }

    public function getAllListingColumns()
    {
        $fields = $this->getSortedListingFields();
        $labels = collect();
        foreach ($fields as $f) {
            $props = $this->getTableColumnProps($f);
            $labels->push($props[0]);
        }

        return $labels;
    }
}
