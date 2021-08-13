<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithComponentCode
{
    public function generateComponentCode()
    {
        $code = [];
        $code['sort'] = $this->generateSortCode();
        $code['search'] = $this->generateSearchCode();
        $code['pagination_dropdown'] = $this->generatePaginationDropdownCode();
        $code['pagination'] = $this->generatePaginationCode();
        $code['with_query'] = $this->generateWithQueryCode();
        $code['with_count_query'] = $this->generateWithCountQueryCode();
        $code['hide_columns'] = $this->generateHideColumnsCode();

        $code['child_delete'] = $this->generateDeleteCode();
        $code['child_add'] = $this->generateAddCode();
        $code['child_edit'] = $this->generateEditCode();
        $code['child_listeners'] = $this->generateChildListeners();
        $code['child_item'] = $this->generateChildItem();
        $code['child_rules'] = $this->generateChildRules();
        $code['child_validation_attributes'] = $this->generateChildValidationAttributes();
        $code['child_other_models'] = $this->generateOtherModelsCode();
        $code['child_vars'] = $this->getVars();
        return $code;
    }

    public function generateSortCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->isSortingEnabled()) {
            $code['vars'] = $this->getSortingVars();
            $code['query'] = $this->getSortingQuery();
            $code['method'] = $this->getSortingMethod();
        }

        return $code;
    }

    public function generateSearchCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->isSearchingEnabled()) {
            $code['vars'] = $this->getSearchingVars();
            $code['query'] = $this->getSearchingQuery();
            $code['method'] = $this->getSearchingMethod();
        }

        return $code;
    }

    public function generatePaginationDropdownCode()
    {
        $code = [
            'method' => ''
        ];
        if ($this->isPaginationDropdownEnabled()) {
            $code['method'] = $this->getPaginationDropdownMethod();
        }
        return $code;
    }

    public function generatePaginationCode()
    {
        $code = [
            'vars' => ''
        ];

        $code['vars'] = $this->getPaginationVars();

        return $code;
    }

    public function generateWithQueryCode()
    {
        $relations = collect();
        foreach ($this->withRelations as $r) {
            $relations->push(
                Str::of($r['relationName'])->append("'")->prepend("'")
            );
        }

        if ($relations->isEmpty()) {
            return '';
        }

        return Str::replace(
            '##RELATIONS##',
            $relations->implode(','),
            $this->getWithQueryTemplate()
        );
    }

    public function generateWithCountQueryCode()
    {
        $relations = collect();
        foreach ($this->withCountRelations as $r) {
            $relations->push(
                Str::of($r['relationName'])->append("'")->prepend("'")
            );
        }

        if ($relations->isEmpty()) {
            return '';
        }

        return Str::replace(
            '##RELATIONS##',
            $relations->implode(','),
            $this->getWithCountQueryTemplate()
        );
    }

    public function generateHideColumnsCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
        ];
        if ($this->isHideColumnsEnabled()) {
            $code['vars'] = $this->getHideColumnVars();
            $code['init'] = $this->getHideColumnInitCode();
        }

        return $code;
    }

    public function generateAddCode()
    {
        $code = [
            'vars' => '',
            'method' => ''
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
            'method' => ''
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
            'method' => ''
        ];
        if ($this->isDeleteFeatureEnabled()) {
            $code['vars'] = $this->getDeleteVars();
            $code['method'] = $this->getDeleteMethod();
        }

        return $code;
    }

    public function generateChildListeners()
    {
        return Str::replace(
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

    public function getSortingVars()
    {
        return Str::replace(
            '##SORT_COLUMN##',
            $this->getDefaultSortableColumn(),
            $this->getSortingVarsTemplate()
        );
    }

    public function getSortingQuery()
    {
        return $this->getSortingQueryTemplate();
    }

    public function getSortingMethod()
    {
        return $this->getSortingMethodTemplate();
    }

    public function getSearchingVars()
    {
        return $this->getSearchingVarsTemplate();
    }

    public function getSearchingQuery()
    {
        $searchQuery = collect();

        $searchableColumns = $this->getSearchableColumns();
        $isFirst = true;
        foreach ($searchableColumns as $f) {
            $searchQuery->push(
                Str::replace(
                    [
                        '##FIRST##',
                        '##COLUMN##',
                    ],
                    [
                        $isFirst ? '$query->where' : $this->newLines(1, 6) . '->orWhere',
                        $f['column'],
                    ],
                    $this->getSearchingQueryWhereTemplate(),
                )
            );
            $isFirst = false;
        }

        return Str::replace(
            '##SEARCH_QUERY##',
            $searchQuery->implode(''),
            $this->getSearchinQueryTemplate()
        );
    }

    public function getSearchingMethod()
    {
        return $this->getSearchingMethodTemplate();
    }

    public function getPaginationVars()
    {
        return Str::replace(
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
        return Str::replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
            ],
            [
                $this->getModelName(),
                $this->componentName,
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
                $this->newLines(1, 3) .
                    Str::replace(
                        [
                            '##COLUMN##',
                            '##DEFAULT_VALUE##',
                        ],
                        [
                            $field['column'],
                            ($field['attributes']['type'] == 'checkbox') ? "0" : "''"
                        ],
                        $this->getCreateFieldTemplate()
                    )
            );
        }

        return Str::replace(
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
                $this->componentName,
                $createFieldHtml->implode(''),
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
        return Str::replace(
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
                $this->componentName,
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
                $this->newLines(1, 2) .
                    $this->getChildFieldCode(
                        $field['column'],
                        Str::of($field['attributes']['rules'])->explode(',')->filter()->join('|')
                    )
            );
        }

        $rules->push($this->getRulesForBelongsToFields());
        return Str::replace('##RULES##', $rules->implode(''), $this->getChildRulesTemplate());
    }

    public function generateChildValidationAttributes()
    {
        $fields = $this->getNormalFormFields();
        $attributes = collect();
        foreach ($fields as $field) {
            $attributes->push(
                $this->newLines(1, 2) .
                    $this->getChildFieldCode(
                        $field['column'],
                        $this->getLabel($field['label'], $field['column'])
                    )
            );
        }

        $attributes->push($this->getAttributesForBelongsToFields());
        return Str::replace(
            '##ATTRIBUTES##',
            $attributes->implode(''),
            $this->getChildValidationAttributesTemplate()
        );
    }

    public function generateOtherModelsCode()
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
        return $modelsCode->implode('');
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

        return $modelsCode->implode('');
    }

    public function getVars()
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
            $vars->push($this->getArrayCode(
                $this->getBtmFieldName($r['relationName'])
            ));
        }
        return $this->newLines() . implode($this->newLines(), $vars->all());
    }

    public function getBelongsToVars()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $vars = collect();
        foreach ($this->belongsToRelations as $r) {
            $vars->push($this->getArrayCode(
                Str::plural($r['relationName'])
            ));
        }
        return $this->newLines() . implode($this->newLines(), $vars->all());
    }

    public function getAddFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['add']);
    }

    public function getEditFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['edit']);
    }

    public function getDeleteFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        return $this->getFlashCode($this->flashMessages['text']['delete']);
    }

    public function getBtmInitCode()
    {
        if (!$this->isBtmAddEnabled()) {
            return '';
        }

        $initCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inAdd']) {
                continue;
            }

            $initCode->push(
                Str::replace(
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
        if (!$this->isBtmAddEnabled()) {
            return '';
        }

        $attachCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inAdd']) {
                continue;
            }

            $attachCode->push(
                Str::replace(
                    [
                        '##RELATION##',
                        '##FIELD_NAME##',
                    ],
                    [
                        $r['relationName'],
                        $this->getBtmFieldName($r['relationName'])
                    ],
                    $this->getBtmAttachTemplate()
                )
            );
        }

        return $attachCode->implode('') . $this->newLines(1);
    }

    public function getBtmFetchCode()
    {
        if (!$this->isBtmEditEnabled()) {
            return '';
        }

        $btmFetchCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inEdit']) {
                continue;
            }

            $btmFetchCode->push(
                Str::replace(
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

        return $this->newLines(1) . $btmFetchCode->implode('');
    }

    public function getBtmUpdateCode()
    {
        if (!$this->isBtmEditEnabled()) {
            return '';
        }

        $btmUpdateCode = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inEdit']) {
                continue;
            }

            $btmUpdateCode->push(
                $this->newLines(1) .
                    Str::replace(
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

        return $btmUpdateCode->implode('');
    }

    public function getRulesForBelongsToFields()
    {

        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $rules = collect();
        foreach ($this->belongsToRelations as $r) {
            $rules->push(
                $this->newLines(1, 2) .
                    $this->getChildFieldCode($r['foreignKey'], 'required')
            );
        }
        return $rules->implode('');
    }

    public function getAttributesForBelongsToFields()
    {

        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $attributes = collect();
        foreach ($this->belongsToRelations as $r) {
            $attributes->push(
                $this->newLines(1, 2) .
                    $this->getChildFieldCode(
                        $r['foreignKey'],
                        Str::ucfirst($r['relationName'])
                    )
            );
        }
        return $attributes->implode('');
    }

    public function getBelongsToInitCode($isAdd = true)
    {
        if ($isAdd && !$this->isBelongsToAddEnabled()) {
            return '';
        }

        if (!$isAdd && !$this->isBelongsToEditEnabled()) {
            return '';
        }

        $initCode = collect();
        foreach ($this->belongsToRelations as $r) {
            $initCode->push(
                $this->newLines(1) .
                    Str::replace(
                        [
                            '##BELONGS_TO_VAR##',
                            '##MODEL##',
                            '##DISPLAY_COLUMN##',
                        ],
                        [
                            Str::plural($r['relationName']),
                            $this->getModelName($r['modelPath']),
                            $r['displayColumn'],
                        ],
                        $this->getBelongsToInitTemplate()
                    )
            );
        }
        return $initCode->implode('');
    }

    public function getBelongsToSaveCode()
    {
        if (!$this->isBelongsToAddEnabled()) {
            return '';
        }

        $saveCode = collect();
        foreach ($this->belongsToRelations as $r) {
            $saveCode->push(
                $this->newLines(1, 3) .
                    Str::replace(
                        [
                            '##COLUMN##',
                            '##DEFAULT_VALUE##',
                        ],
                        [
                            $r['foreignKey'],
                            0
                        ],
                        $this->getCreateFieldTemplate()
                    )
            );
        }
        return $saveCode->implode('');
    }

    public function getOtherModelCode($modelPath)
    {
        return Str::replace(
            '##MODEL##',
            $modelPath,
            $this->getOtherModelTemplate()
        );
    }

    public function getArrayCode($name)
    {
        return Str::replace(
            '##NAME##',
            $name,
            $this->getArrayTemplate()
        );
    }

    public function getFlashCode($message)
    {
        if (empty($message)) {
            return '';
        }

        return Str::replace(
            '##MESSAGE##',
            $message,
            $this->getFlashTriggerTemplate()
        );
    }

    public function getChildFieldCode($columnName, $value)
    {
        return Str::replace(
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

    public function getHideColumnVars()
    {
        return Str::replace(
            '##COLUMNS##',
            $this->getAllListingColumns(),
            $this->getHideColumnVarsTemplate()
        ) . 
        $this->newLines() .
        Str::replace(
            '##NAME##',
            'selectedColumns',
            $this->getArrayTemplate()
        );
    }

    public function getHideColumnInitCode()
    {
        return $this->getHideColumnInitCodeTemplate();
    }

    public function getAllListingColumns()
    {
        $fields = $this->getSortedListingFields();
        $labels = collect();
        foreach ($fields as $f) {
            [$label, $column, $isSortable] = $this->getTableColumnProps($f);
            $labels->push($label);
        }

        $columns = collect();
        $labels->each(function ($label)  use($columns) {
            $columns->push(
                Str::replace(
                    '##VALUE##',
                    $label,
                    $this->getArrayValueTemplate()
                )
            );
        });

        return $columns->implode("\n");
    }
}
