<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

trait WithComponentCode
{
    public function generateComponentCode()
    {

        $this->tallComponent = App::make(TallComponent::class);
        //Sorting
        $this->tallComponent->setSortingFlag($this->isSortingEnabled());
        if($this->tallComponent->getSortingFlag()) {
            $this->tallComponent->setDefaultSortableColumn($this->getDefaultSortableColumn());
        }
        //Searching
        $this->tallComponent->setSearchingFlag($this->isSearchingEnabled());
        if($this->tallComponent->getSearchingFlag()) {
            $this->tallComponent->setSearchableColumns($this->getSearchableColumns());
        }
        //Pagination Dropdown
        $this->tallComponent->setPaginationDropdownFlag($this->isPaginationDropdownEnabled());
        //Records Per Page
        $this->tallComponent->setRecordsPerPage($this->advancedSettings['table_settings']['recordsPerPage']);
        //Eager Load Models
        $this->tallComponent->setEagerLoadModels($this->withRelations);
        //Eager Load Count Models
        $this->tallComponent->setEagerLoadCountModels($this->withCountRelations);
        //Hide Flag
        $this->tallComponent->setHideColumnsFlag($this->isHideColumnsEnabled());
        if($this->tallComponent->getHideColumnsFlag()) {
            $this->tallComponent->setListingColumns($this->getAllListingColumns());
        }
        
        //Other Models
        $this->tallComponent->setOtherModels($this->filters);

        $code = [];
        $code['sort'] = $this->tallComponent->getSortCode();
        $code['search'] = $this->tallComponent->getSearchCode();
        $code['pagination_dropdown'] = $this->tallComponent->getPaginationDropdownCode();
        $code['pagination'] = $this->tallComponent->getPaginationCode();
        $code['with_query'] = $this->tallComponent->getWithQueryCode();
        $code['with_count_query'] = $this->tallComponent->getWithCountQueryCode();
        $code['hide_columns'] = $this->tallComponent->getHideColumnsCode();
        $code['bulk_actions'] = $this->generateBulkActionsCode();
        $code['filter'] = $this->generateFilterCode();
        $code['other_models'] = $this->tallComponent->getOtherModelsCode();

        $code['child_delete'] = $this->generateDeleteCode();
        $code['child_add'] = $this->generateAddCode();
        $code['child_edit'] = $this->generateEditCode();
        $code['child_listeners'] = $this->generateChildListeners();
        $code['child_item'] = $this->generateChildItem();
        $code['child_rules'] = $this->generateChildRules();
        $code['child_validation_attributes'] = $this->generateChildValidationAttributes();
        $code['child_other_models'] = $this->generateChildOtherModelsCode();
        $code['child_vars'] = $this->getRelationVars();

        return $code;
    }

    public function generateFilterCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->isFilterEnabled()) {
            $code['vars'] = $this->getFilterVars();
            $code['init'] = $this->getFilterInitCode();
            $code['query'] = $this->getFilterQuery();
            $code['method'] = $this->getFilterMethod();
        }

        return $code;
    }

    public function generateBulkActionsCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->isBulkActionsEnabled()) {
            $code['vars'] = $this->getBulkActionsVars();
            $code['method'] = $this->getBulkActionMethod();
        }

        return $code;
    }

    public function generateAddCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->isAddFeatureEnabled()) {
            $code['vars'] = $this->getAddVars();
            $code['method'] = $this->getAddMethod();
        }

        return $code;
    }

    public function generateEditCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->isEditFeatureEnabled()) {
            $code['vars'] = $this->getEditVars();
            $code['method'] = $this->getEditMethod();
        }

        return $code;
    }

    public function generateDeleteCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->isDeleteFeatureEnabled()) {
            $code['vars'] = $this->getDeleteVars();
            $code['method'] = $this->getDeleteMethod();
        }

        return $code;
    }

    public function generateChildListeners()
    {
        return str_replace(
            [
                '##DELETE_LISTENER##',
                '##ADD_LISTENER##',
                '##EDIT_LISTENER##',
            ],
            [
                $this->isDeleteFeatureEnabled() ? 'showDeleteForm' : '',
                $this->isAddFeatureEnabled() ? 'showCreateForm' : '',
                $this->isEditFeatureEnabled() ? 'showEditForm' : '',
            ],
            $this->getChildListenerTemplate(),
        );
    }

    public function getPaginationVars()
    {
        return str_replace(
            '##PER_PAGE##',
            $this->advancedSettings['table_settings']['recordsPerPage'],
            $this->getPaginationVarsTemplate()
        );
    }

    public function getPaginationDropdownMethod()
    {
        return $this->getPaginationDropdownMethodTemplate();
    }

    public function getDeleteVars()
    {
        return $this->getDeleteVarsTemplate();
    }

    public function getDeleteMethod()
    {
        return str_replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
            ],
            [
                $this->getModelName(),
                $this->getComponentName(),
                $this->getDeleteFlashCode(),
            ],
            $this->getDeleteMethodTemplate()
        );
    }

    public function getAddVars()
    {
        return $this->getAddVarsTemplate();
    }

    public function getAddMethod()
    {
        $fields = $this->getNormalFormFields(true, false);
        $createFieldHtml = collect();
        foreach ($fields as $field) {
            $createFieldHtml->push(
                str_replace(
                    [
                        '##COLUMN##',
                        '##DEFAULT_VALUE##',
                    ],
                    [
                        $field['column'],
                        ($field['attributes']['type'] == 'checkbox') ? '0' : "''",
                    ],
                    $this->getCreateFieldTemplate()
                )
            );
        }

        return str_replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##CREATE_FIELDS##',
                '##FLASH_MESSAGE##',
                '##BTM_INIT##',
                '##BTM_ATTACH##',
                '##BELONGS_TO_INIT##',
                '##BELONGS_TO_SAVE##',
            ],
            [
                $this->getModelName(),
                $this->getComponentName(),
                $createFieldHtml->prependAndJoin($this->newLines(1, 3)),
                $this->getAddFlashCode(),
                $this->getBtmInitCode(),
                $this->getBtmAttachCode(),
                $this->getBelongsToInitCode(),
                $this->getBelongsToSaveCode(),
            ],
            $this->getAddMethodTemplate()
        );
    }

    public function getEditVars()
    {
        return $this->getEditVarsTemplate();
    }

    public function getEditMethod()
    {
        return str_replace(
            [
                '##MODEL##',
                '##MODEL_VAR##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
                '##BTM_FETCH##',
                '##BTM_UPDATE##',
                '##BELONGS_TO_INIT##',
            ],
            [
                $this->getModelName(),
                Str::lower($this->getModelName()),
                $this->getComponentName(),
                $this->getEditFlashCode(),
                $this->getBtmFetchCode(),
                $this->getBtmUpdateCode(),
                $this->getBelongsToInitCode(false),
            ],
            $this->getEditMethodTemplate()
        );
    }

    public function generateChildItem()
    {
        return $this->getChildItemTemplate();
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
        return $this->generateBtmModelsCode().$this->generateBelongstoModelsCode();
    }

    public function generateBtmModelsCode()
    {
        if (! $this->isBtmEnabled()) {
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
        if (! $this->isBelongsToEnabled()) {
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
        return $this->getBtmVars().$this->getBelongsToVars();
    }

    public function getBtmVars()
    {
        if (! $this->isBtmEnabled()) {
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
        if (! $this->isBelongsToEnabled()) {
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

    public function getAddFlashCode()
    {
        if (! $this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['add']);
    }

    public function getEditFlashCode()
    {
        if (! $this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['edit']);
    }

    public function getDeleteFlashCode()
    {
        if (! $this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['delete']);
    }

    public function getBtmInitCode()
    {
        if (! $this->isBtmAddEnabled()) {
            return '';
        }

        $initCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (! $r['inAdd']) {
                continue;
            }

            $initCode->push(
                str_replace(
                    [
                        '##RELATION##',
                        '##MODEL##',
                        '##FIELD_NAME##',
                        '##DISPLAY_COLUMN##',
                    ],
                    [
                        $r['relationName'],
                        $this->getModelName($r['modelPath']),
                        $this->getBtmFieldName($r['relationName']),
                        $r['displayColumn'],
                    ],
                    $this->getBtmInitTemplate()
                )
            );
        }

        return $initCode->implode('');
    }

    public function getBtmAttachCode()
    {
        if (! $this->isBtmAddEnabled()) {
            return '';
        }

        $attachCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (! $r['inAdd']) {
                continue;
            }

            $attachCode->push(
                str_replace(
                    [
                        '##RELATION##',
                        '##FIELD_NAME##',
                    ],
                    [
                        $r['relationName'],
                        $this->getBtmFieldName($r['relationName']),
                    ],
                    $this->getBtmAttachTemplate()
                )
            );
        }

        return $attachCode->implode('').$this->newLines();
    }

    public function getBtmFetchCode()
    {
        if (! $this->isBtmEditEnabled()) {
            return '';
        }

        $btmFetchCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (! $r['inEdit']) {
                continue;
            }

            $btmFetchCode->push(
                str_replace(
                    [
                        '##RELATION##',
                        '##FIELD_NAME##',
                        '##KEY##',
                        '##MODEL##',
                        '##MODEL_VAR##',
                        '##DISPLAY_COLUMN##',
                    ],
                    [
                        $r['relationName'],
                        $this->getBtmFieldName($r['relationName']),
                        $r['relatedKey'],
                        $this->getModelName($r['modelPath']),
                        Str::lower($this->getModelName()),
                        $r['displayColumn'],
                    ],
                    $this->getBtmFetchTemplate()
                )
            );
        }

        return $btmFetchCode->prependAndJoin('', $this->newLines());
    }

    public function getBtmUpdateCode()
    {
        if (! $this->isBtmEditEnabled()) {
            return '';
        }

        $btmUpdateCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (! $r['inEdit']) {
                continue;
            }

            $btmUpdateCode->push(
                str_replace(
                    [
                        '##RELATION##',
                        '##FIELD_NAME##',
                    ],
                    [
                        $r['relationName'],
                        $this->getBtmFieldName($r['relationName']),
                    ],
                    $this->getBtmUpdateTemplate()
                )
            );
        }

        return $btmUpdateCode->prependAndJoin($this->newLines());
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
        if (! $this->isBelongsToEnabled()) {
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

    public function getBelongsToInitCode($isAdd = true)
    {
        if ($isAdd && ! $this->isBelongsToAddEnabled()) {
            return '';
        }

        if (! $isAdd && ! $this->isBelongsToEditEnabled()) {
            return '';
        }

        $initCode = collect();
        foreach ($this->belongsToRelations as $r) {
            $initCode->push(
                str_replace(
                    [
                        '##BELONGS_TO_VAR##',
                        '##MODEL##',
                        '##DISPLAY_COLUMN##',
                    ],
                    [
                        $this->getBelongsToVarName($r['relationName']),
                        $this->getModelName($r['modelPath']),
                        $r['displayColumn'],
                    ],
                    $this->getBelongsToInitTemplate()
                )
            );
        }

        return $initCode->prependAndJoin($this->newLines());
    }

    public function getBelongsToSaveCode()
    {
        if (! $this->isBelongsToAddEnabled()) {
            return '';
        }

        $saveCode = collect();
        foreach ($this->belongsToRelations as $r) {
            $saveCode->push(
                str_replace(
                    [
                        '##COLUMN##',
                        '##DEFAULT_VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        0,
                    ],
                    $this->getCreateFieldTemplate()
                )
            );
        }

        return $saveCode->prependAndJoin($this->newLines(1, 3));
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
            $this->getFlashTriggerTemplate()
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

    public function getBulkActionsVars()
    {
        return $this->newLines().$this->getArrayCode('selectedItems');
    }

    public function getBulkActionMethod()
    {
        return str_replace(
            [
                '##MODEL##',
                '##PRIMARY_KEY##',
                '##COLUMN##',
            ],
            [
                $this->getModelName(),
                $this->getPrimaryKey(),
                $this->advancedSettings['table_settings']['bulkActionColumn'],
            ],
            $this->getBulkActionMethodTemplate()
        );
    }

    public function getFilterVars()
    {
        $vars = collect();
        $vars->push($this->getArrayCode('filters'));
        $vars->push($this->getArrayCode('selectedFilters'));

        return $vars->prependAndJoin($this->newLines());
    }

    public function getFilterInitCode()
    {
        return $this->getNoRelationFilterInitCode().
            $this->getRelationFilterInitCode('BelongsTo').
            $this->getRelationFilterInitCode('BelongsToMany');
    }

    public function getNoRelationFilterInitCode()
    {
        $filters = collect();
        foreach ($this->filters as $f) {
            if ($f['type'] != 'None') {
                continue;
            }
            $filterOptions = $this->generateFilterOptionsFromJson($f);
            if ($filterOptions->isEmpty()) {
                continue;
            }
            $filters->push(
                str_replace(
                    [
                        '##KEY##',
                        '##LABEL##',
                        '##OPTIONS##',
                    ],
                    [
                        $this->getFilterColumnName($f),
                        $this->getFilterLabelName($f),
                        $filterOptions->prependAndJoin($this->newLines(1, 5)),
                    ],
                    $this->getNoRelationFilterInitTemplate()
                )
            );
        }

        if ($filters->isEmpty()) {
            return '';
        }

        return str_replace(
            '##FILTERS##',
            $filters->prependAndJoin($this->newLines(1, 1)).$this->newLines(1, 2),
            $this->getFilterInitTemplate()
        );
    }

    public function getRelationFilterInitCode($type)
    {
        $filters = collect();
        foreach ($this->filters as $f) {
            if ($f['type'] != $type) {
                continue;
            }
            $filters->push(
                str_replace(
                    [
                        '##VAR##',
                        '##MODEL##',
                        '##COLUMN##',
                        '##OWNER_KEY##',
                        '##FOREIGN_KEY##',
                        '##LABEL##',
                    ],
                    [
                        Str::plural($f['relation']),
                        $this->getModelName($f['modelPath']),
                        $f['column'],
                        $this->getFilterOwnerKey($f),
                        $this->getFilterForeignKey($f),
                        $this->getFilterLabelName($f),
                    ],
                    $this->getRelationFilterInitTemplate()
                )
            );
        }

        return $filters->implode('');
    }

    public function generateFilterOptionsFromJson($f)
    {
        $filterOptions = collect();
        $options = json_decode($f['options']);
        if (is_null($options)) {
            return $filterOptions;
        }

        foreach ($options as $k => $v) {
            $filterOptions->push(
                str_replace(
                    [
                        '##KEY##',
                        '##LABEL##',
                    ],
                    [
                        $k,
                        $v,
                    ],
                    $this->getFilterOptionTemplate()
                )
            );
        }

        return $filterOptions;
    }

    public function getFilterMethod()
    {
        return $this->getFilterMethodTemplate();
    }

    public function getFilterQuery()
    {
        $query = collect();
        foreach ($this->filters as $f) {
            if ($f['type'] == 'BelongsToMany') {
                $query->push(
                    str_replace(
                        [
                            '##COLUMN##',
                            '##RELATION##',
                            '##RELATED_KEY##',
                            '##TABLE##',
                        ],
                        [
                            $f['relation'].'_'.$f['relatedKey'],
                            $f['relation'],
                            $f['relatedKey'],
                            $f['relatedTableName'],
                        ],
                        $this->getFilterQueryBtmTemplate()
                    )
                );
            } else {
                $query->push(
                    str_replace(
                        '##COLUMN##',
                        $this->getFilterColumnName($f),
                        $this->getFilterQueryTemplate()
                    )
                );
            }
        }

        return $query->prependAndJoin($this->newLines());
    }
}
