<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithComponentCode
{
    private function _generateComponentCode()
    {
        $return = [];
        $return['sort'] = $this->_generateSortCode();
        $return['search'] = $this->_generateSearchCode();
        $return['pagination_dropdown'] = $this->_generatePaginationDropdownCode();
        $return['pagination'] = $this->_generatePaginationCode();
        $return['with_query'] = $this->_generateWithQueryCode();
        $return['with_count_query'] = $this->_generateWithCountQueryCode();

        $return['child_delete'] = $this->_generateDeleteCode();
        $return['child_add'] = $this->_generateAddCode();
        $return['child_edit'] = $this->_generateEditCode();
        $return['child_listeners'] = $this->_generateChildListeners();
        $return['child_item'] = $this->_generateChildItem();
        $return['child_rules'] = $this->_generateChildRules();
        $return['child_validation_attributes'] = $this->_generateChildValidationAttributes();
        $return['child_other_models'] = $this->_generateOtherModelsCode();
        $return['child_vars'] = $this->_getVars();
        return $return;
    }

    private function _generateSortCode()
    {
        $return = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->_isSortingEnabled()) {
            $return['vars'] = $this->_getSortingVars();
            $return['query'] = $this->_getSortingQuery();
            $return['method'] = $this->_getSortingMethod();
        }

        return $return;
    }

    private function _generateSearchCode()
    {
        $return = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->_isSearchingEnabled()) {
            $return['vars'] = $this->_getSearchingVars();
            $return['query'] = $this->_getSearchingQuery();
            $return['method'] = $this->_getSearchingMethod();
        }

        return $return;
    }

    private function _generatePaginationDropdownCode()
    {
        $return = [
            'method' => ''
        ];
        if ($this->_isPaginationDropdownEnabled()) {
            $return['method'] = $this->_getPaginationDropdownMethod();
        }
        return $return;
    }

    private function _generatePaginationCode()
    {
        $return = [
            'vars' => ''
        ];

        $return['vars'] = $this->_getPaginationVars();

        return $return;
    }

    private function _generateWithQueryCode()
    {
        $collection = collect();
        foreach ($this->withRelations as $r) {
            $collection->push("'" . $r['relationName'] . "'");
        }

        if ($collection->isEmpty()) {
            return '';
        }

        return Str::replace('##RELATIONS##', $collection->implode(','), $this->_getWithQueryTemplate());
    }

    private function _generateWithCountQueryCode()
    {
        $collection = collect();
        foreach ($this->withCountRelations as $r) {
            $collection->push("'" . $r['relationName'] . "'");
        }

        if ($collection->isEmpty()) {
            return '';
        }

        return Str::replace('##RELATIONS##', $collection->implode(','), $this->_getWithCountQueryTemplate());
    }

    private function _generateAddCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->_isAddFeatureEnabled()) {
            $return['vars'] = $this->_getAddVars();
            $return['method'] = $this->_getAddMethod();
        }

        return $return;
    }

    private function _generateEditCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->_isEditFeatureEnabled()) {
            $return['vars'] = $this->_getEditVars();
            $return['method'] = $this->_getEditMethod();
        }

        return $return;
    }

    private function _generateDeleteCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->_isDeleteFeatureEnabled()) {
            $return['vars'] = $this->_getDeleteVars();
            $return['method'] = $this->_getDeleteMethod();
        }

        return $return;
    }

    private function _generateChildListeners()
    {
        $return = '';
        return Str::replace(
            [
                '##DELETE_LISTENER##',
                '##ADD_LISTENER##',
                '##EDIT_LISTENER##',
            ],
            [
                $this->_isDeleteFeatureEnabled() ? 'showDeleteForm' : '',
                $this->_isAddFeatureEnabled() ? 'showCreateForm' : '',
                $this->_isEditFeatureEnabled() ? 'showEditForm' : '',
            ],
            $this->_getChildListenerTemplate(),
        );

        return $return;
    }

    private function _getSortingVars()
    {
        return Str::replace('##SORT_COLUMN##', $this->_getDefaultSortableColumn(), $this->_getSortingVarsTemplate());
    }

    private function _getSortingQuery()
    {
        return $this->_getSortingQueryTemplate();
    }

    private function _getSortingMethod()
    {
        return $this->_getSortingMethodTemplate();
    }

    private function _getSearchingVars()
    {
        return $this->_getSearchingVarsTemplate();
    }

    private function _getSearchingQuery()
    {
        $searchQuery = '';

        $searchableColumns = $this->_getSearchableColumns();
        $isFirst = true;
        foreach ($searchableColumns as $f) {
            $searchQuery .= Str::replace(
                [
                    '##FIRST##',
                    '##COLUMN##',
                ],
                [
                    $isFirst ? '$query->where' : $this->_newLines(1, 6) . '->orWhere',
                    $f['column'],
                ],
                $this->_getSearchingQueryWhereTemplate(),
            );
            $isFirst = false;
        }

        return Str::replace('##SEARCH_QUERY##', $searchQuery, $this->_getSearchinQueryTemplate());
    }

    private function _getSearchingMethod()
    {
        return $this->_getSearchingMethodTemplate();
    }

    private function _getPaginationVars()
    {
        return Str::replace('##PER_PAGE##', $this->advancedSettings['table_settings']['records_per_page'], $this->_getPaginationVarsTemplate());
    }

    private function _getPaginationDropdownMethod()
    {
        return $this->_getPaginationDropdownMethodTemplate();
    }

    private function _getDeleteVars()
    {
        return $this->_getDeleteVarsTemplate();
    }

    private function _getDeleteMethod()
    {
        return Str::replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##FLASH_MESSAGE##',
            ],
            [
                $this->_getModelName(),
                $this->componentName,
                $this->_getDeleteFlashCode(),
            ],
            $this->_getDeleteMethodTemplate()
        );
    }

    private function _getAddVars()
    {
        return $this->_getAddVarsTemplate();
    }

    private function _getAddMethod()
    {
        $fields = $this->_getFormFields(true, false);
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->_newLines(1, 3) .
                Str::replace(
                    [
                        '##COLUMN##',
                        '##DEFAULT_VALUE##',
                    ],
                    [
                        $field['column'],
                        ($field['attributes']['type'] == 'checkbox') ? "0" : "''"
                    ],
                    $this->_getCreateFieldTemplate()
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
                $this->_getModelName(),
                $this->componentName,
                $string,
                $this->_getAddFlashCode(),
                $this->_getBtmInitCode(),
                $this->_getBtmAttachCode(),
                $this->_getBelongsToInitCode(),
                $this->_getBelongsToSaveCode(),
            ],
            $this->_getAddMethodTemplate()
        );
    }

    private function _getEditVars()
    {
        return $this->_getEditVarsTemplate();
    }

    private function _getEditMethod()
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
                $this->_getModelName(),
                Str::lower($this->_getModelName()),
                $this->componentName,
                $this->_getEditFlashCode(),
                $this->_getBtmFetchCode(),
                $this->_getBtmUpdateCode(),
                $this->_getBelongsToInitCode(false),
            ],
            $this->_getEditMethodTemplate()
        );
    }

    private function _generateChildItem()
    {
        return $this->_getChildItemTemplate();
    }

    private function _generateChildRules()
    {
        $fields = $this->_getFormFields();
        $string = '';

        foreach ($fields as $field) {
            $string .= $this->_newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $field['column'],
                        Str::of($field['attributes']['rules'])->explode(',')->filter()->join('|')
                    ],
                    $this->_getChildFieldTemplate()
                );
        }

        $string .= $this->_getRulesForBelongsToFields();
        return Str::replace('##RULES##', $string, $this->_getChildRulesTemplate());
    }

    private function _generateChildValidationAttributes()
    {
        $fields = $this->_getFormFields();
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->_newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $field['column'],
                        $this->_getLabel($field['label'], $field['column'])
                    ],
                    $this->_getChildFieldTemplate()
                );
        }
        $string .= $this->_getAttributesForBelongsToFields();
        return Str::replace('##ATTRIBUTES##', $string, $this->_getchildValidationAttributesTemplate());
    }

    private function _generateOtherModelsCode()
    {
        return $this->_generateBtmModelsCode() . $this->_generateBelongstoModelsCode();
    }

    private function _generateBtmModelsCode()
    {
        if (!$this->_isBtmEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            $string .= Str::replace('##MODEL##', $r['modelPath'], $this->_getOtherModelTemplate());
        }

        return $string;
    }

    private function _generateBelongstoModelsCode()
    {
        if (!$this->_isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= Str::replace('##MODEL##', $r['modelPath'], $this->_getOtherModelTemplate());
        }

        return $string;
    }

    private function _getVars()
    {
        return $this->_getBtmVars() . $this->_getBelongsToVars();
    }

    private function _getBtmVars()
    {
        if (!$this->_isBtmEnabled()) {
            return '';
        }

        $collect = collect();
        foreach ($this->belongsToManyRelations as $r) {
            $collect->push(Str::replace('##NAME##', $r['relationName'], $this->_getArrayTemplate()));
            $collect->push(Str::replace('##NAME##', $this->_getBtmFieldName($r['relationName']), $this->_getArrayTemplate()));
        }
        return $this->_newLines() . implode($this->_newLines(), $collect->all());
    }

    private function _getBelongsToVars()
    {
        if (!$this->_isBelongsToEnabled()) {
            return '';
        }
        $collect = collect();
        foreach ($this->belongsToRelations as $r) {
            $collect->push(Str::replace('##NAME##', Str::plural($r['relationName']), $this->_getArrayTemplate()));
        }
        return $this->_newLines() . implode($this->_newLines(), $collect->all());
    }

    private function _getAddFlashCode()
    {
        if (!$this->_isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['add'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['add'], $this->_getFlashTriggerTemplate());
    }

    private function _getEditFlashCode()
    {
        if (!$this->_isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['edit'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['edit'], $this->_getFlashTriggerTemplate());
    }

    private function _getDeleteFlashCode()
    {
        if (!$this->_isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['delete'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['delete'], $this->_getFlashTriggerTemplate());
    }

    private function _getBtmInitCode()
    {
        if (!$this->_isBtmAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['in_add']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##RELATION##',
                    '##MODEL##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->_getModelName($r['modelPath']),
                    $this->_getBtmFieldName($r['relationName'])
                ],
                $this->_getBtmInitTemplate()
            );
        }

        return $string;
    }

    private function _getBtmAttachCode()
    {
        if (!$this->_isBtmAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['in_add']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->_getBtmFieldName($r['relationName'])
                ],
                $this->_getBtmAttachTemplate()
            );
        }

        return $string . $this->_newLines(1);
    }

    private function _getBtmFetchCode()
    {
        if (!$this->_isBtmEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['in_edit']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                    '##KEY##',
                    '##MODEL##',
                    '##MODEL_VAR##',
                ],
                [
                    $r['relationName'],
                    $this->_getBtmFieldName($r['relationName']),
                    $r['relatedKey'],
                    $this->_getModelName($r['modelPath']),
                    Str::lower($this->_getModelName()),
                ],
                $this->_getBtmFetchTemplate()
            );
        }

        return $this->_newLines(1) . $string;
    }

    private function _getBtmUpdateCode()
    {
        if (!$this->_isBtmEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['in_edit']) {
                continue;
            }

            $string .= $this->_newLines(1) .
                Str::replace(
                    [
                        '##RELATION##',
                        '##FIELD_NAME##',
                    ],
                    [
                        $r['relationName'],
                        $this->_getBtmFieldName($r['relationName']),
                    ],
                    $this->_getBtmUpdateTemplate()
                );
        }

        return $string;
    }

    private function _getRulesForBelongsToFields()
    {

        if (!$this->_isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->_newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        'required'
                    ],
                    $this->_getChildFieldTemplate()
                );
        }
        return $string;
    }

    private function _getAttributesForBelongsToFields()
    {

        if (!$this->_isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->_newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        Str::ucfirst($r['relationName']),
                    ],
                    $this->_getChildFieldTemplate()
                );
        }
        return $string;
    }

    private function _getBelongsToInitCode($isAdd = true)
    {
        if ($isAdd && !$this->_isBelongsToAddEnabled()) {
            return '';
        }

        if (!$isAdd && !$this->_isBelongsToEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->_newLines(1) .
                Str::replace(
                    [
                        '##BELONGS_TO_VAR##',
                        '##MODEL##',
                    ],
                    [
                        Str::plural($r['relationName']),
                        $this->_getModelName($r['modelPath']),
                    ],
                    $this->_getBelongsToInitTemplate()
                );
        }
        return $string;
    }

    private function _getBelongsToSaveCode()
    {
        if (!$this->_isBelongsToAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->_newLines(1, 3) .
                Str::replace(
                    [
                        '##COLUMN##',
                        '##DEFAULT_VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        0
                    ],
                    $this->_getCreateFieldTemplate()
                );
        }
        return $string;
    }
}
