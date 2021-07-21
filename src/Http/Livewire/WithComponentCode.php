<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

trait WithComponentCode
{
    public function generateComponentCode()
    {
        $return = [];
        $return['sort'] = $this->generateSortCode();
        $return['search'] = $this->generateSearchCode();
        $return['pagination_dropdown'] = $this->generatePaginationDropdownCode();
        $return['pagination'] = $this->generatePaginationCode();
        $return['with_query'] = $this->generateWithQueryCode();
        $return['with_count_query'] = $this->generateWithCountQueryCode();

        $return['child_delete'] = $this->generateDeleteCode();
        $return['child_add'] = $this->generateAddCode();
        $return['child_edit'] = $this->generateEditCode();
        $return['child_listeners'] = $this->generateChildListeners();
        $return['child_item'] = $this->generateChildItem();
        $return['child_rules'] = $this->generateChildRules();
        $return['child_validation_attributes'] = $this->generateChildValidationAttributes();
        $return['child_other_models'] = $this->generateOtherModelsCode();
        $return['child_vars'] = $this->getVars();
        return $return;
    }

    public function generateSortCode()
    {
        $return = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->isSortingEnabled()) {
            $return['vars'] = $this->getSortingVars();
            $return['query'] = $this->getSortingQuery();
            $return['method'] = $this->getSortingMethod();
        }

        return $return;
    }

    public function generateSearchCode()
    {
        $return = [
            'vars' => '',
            'query' => '',
            'method' => ''
        ];
        if ($this->isSearchingEnabled()) {
            $return['vars'] = $this->getSearchingVars();
            $return['query'] = $this->getSearchingQuery();
            $return['method'] = $this->getSearchingMethod();
        }

        return $return;
    }

    public function generatePaginationDropdownCode()
    {
        $return = [
            'method' => ''
        ];
        if ($this->isPaginationDropdownEnabled()) {
            $return['method'] = $this->getPaginationDropdownMethod();
        }
        return $return;
    }

    public function generatePaginationCode()
    {
        $return = [
            'vars' => ''
        ];

        $return['vars'] = $this->getPaginationVars();

        return $return;
    }

    public function generateWithQueryCode()
    {
        $collection = collect();
        foreach ($this->withRelations as $r) {
            $collection->push("'" . $r['relationName'] . "'");
        }

        if ($collection->isEmpty()) {
            return '';
        }

        return Str::replace('##RELATIONS##', $collection->implode(','), $this->getWithQueryTemplate());
    }

    public function generateWithCountQueryCode()
    {
        $collection = collect();
        foreach ($this->withCountRelations as $r) {
            $collection->push("'" . $r['relationName'] . "'");
        }

        if ($collection->isEmpty()) {
            return '';
        }

        return Str::replace('##RELATIONS##', $collection->implode(','), $this->getWithCountQueryTemplate());
    }

    public function generateAddCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->isAddFeatureEnabled()) {
            $return['vars'] = $this->getAddVars();
            $return['method'] = $this->getAddMethod();
        }

        return $return;
    }

    public function generateEditCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->isEditFeatureEnabled()) {
            $return['vars'] = $this->getEditVars();
            $return['method'] = $this->getEditMethod();
        }

        return $return;
    }

    public function generateDeleteCode()
    {
        $return = [
            'vars' => '',
            'method' => ''
        ];
        if ($this->isDeleteFeatureEnabled()) {
            $return['vars'] = $this->getDeleteVars();
            $return['method'] = $this->getDeleteMethod();
        }

        return $return;
    }

    public function generateChildListeners()
    {
        $return = '';
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

        return $return;
    }

    public function getSortingVars()
    {
        return Str::replace('##SORT_COLUMN##', $this->getDefaultSortableColumn(), $this->getSortingVarsTemplate());
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
        $searchQuery = '';

        $searchableColumns = $this->getSearchableColumns();
        $isFirst = true;
        foreach ($searchableColumns as $f) {
            $searchQuery .= Str::replace(
                [
                    '##FIRST##',
                    '##COLUMN##',
                ],
                [
                    $isFirst ? '$query->where' : $this->newLines(1, 6) . '->orWhere',
                    $f['column'],
                ],
                $this->getSearchingQueryWhereTemplate(),
            );
            $isFirst = false;
        }

        return Str::replace('##SEARCH_QUERY##', $searchQuery, $this->getSearchinQueryTemplate());
    }

    public function getSearchingMethod()
    {
        return $this->getSearchingMethodTemplate();
    }

    public function getPaginationVars()
    {
        return Str::replace('##PER_PAGE##', $this->advancedSettings['table_settings']['recordsPerPage'], $this->getPaginationVarsTemplate());
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
        $fields = $this->getFormFields(true, false);
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->newLines(1, 3) .
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
                $string,
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
        $fields = $this->getFormFields();
        $string = '';

        foreach ($fields as $field) {
            $string .= $this->newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $field['column'],
                        Str::of($field['attributes']['rules'])->explode(',')->filter()->join('|')
                    ],
                    $this->getChildFieldTemplate()
                );
        }

        $string .= $this->getRulesForBelongsToFields();
        return Str::replace('##RULES##', $string, $this->getChildRulesTemplate());
    }

    public function generateChildValidationAttributes()
    {
        $fields = $this->getFormFields();
        $string = '';
        foreach ($fields as $field) {
            $string .= $this->newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $field['column'],
                        $this->getLabel($field['label'], $field['column'])
                    ],
                    $this->getChildFieldTemplate()
                );
        }
        $string .= $this->getAttributesForBelongsToFields();
        return Str::replace('##ATTRIBUTES##', $string, $this->getchildValidationAttributesTemplate());
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

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            $string .= Str::replace('##MODEL##', $r['modelPath'], $this->getOtherModelTemplate());
        }

        return $string;
    }

    public function generateBelongstoModelsCode()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= Str::replace('##MODEL##', $r['modelPath'], $this->getOtherModelTemplate());
        }

        return $string;
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

        $collect = collect();
        foreach ($this->belongsToManyRelations as $r) {
            $collect->push(Str::replace('##NAME##', $r['relationName'], $this->getArrayTemplate()));
            $collect->push(Str::replace('##NAME##', $this->getBtmFieldName($r['relationName']), $this->getArrayTemplate()));
        }
        return $this->newLines() . implode($this->newLines(), $collect->all());
    }

    public function getBelongsToVars()
    {
        if (!$this->isBelongsToEnabled()) {
            return '';
        }
        $collect = collect();
        foreach ($this->belongsToRelations as $r) {
            $collect->push(Str::replace('##NAME##', Str::plural($r['relationName']), $this->getArrayTemplate()));
        }
        return $this->newLines() . implode($this->newLines(), $collect->all());
    }

    public function getAddFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['add'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['add'], $this->getFlashTriggerTemplate());
    }

    public function getEditFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['edit'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['edit'], $this->getFlashTriggerTemplate());
    }

    public function getDeleteFlashCode()
    {
        if (!$this->isFlashMessageEnabled()) {
            return '';
        }

        if (empty($this->flashMessages['text']['delete'])) {
            return '';
        }

        return Str::replace('##MESSAGE##', $this->flashMessages['text']['delete'], $this->getFlashTriggerTemplate());
    }

    public function getBtmInitCode()
    {
        if (!$this->isBtmAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inAdd']) {
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
                    $this->getModelName($r['modelPath']),
                    $this->getBtmFieldName($r['relationName'])
                ],
                $this->getBtmInitTemplate()
            );
        }

        return $string;
    }

    public function getBtmAttachCode()
    {
        if (!$this->isBtmAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inAdd']) {
                continue;
            }

            $string .= Str::replace(
                [
                    '##RELATION##',
                    '##FIELD_NAME##',
                ],
                [
                    $r['relationName'],
                    $this->getBtmFieldName($r['relationName'])
                ],
                $this->getBtmAttachTemplate()
            );
        }

        return $string . $this->newLines(1);
    }

    public function getBtmFetchCode()
    {
        if (!$this->isBtmEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inEdit']) {
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
                    $this->getBtmFieldName($r['relationName']),
                    $r['relatedKey'],
                    $this->getModelName($r['modelPath']),
                    Str::lower($this->getModelName()),
                ],
                $this->getBtmFetchTemplate()
            );
        }

        return $this->newLines(1) . $string;
    }

    public function getBtmUpdateCode()
    {
        if (!$this->isBtmEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToManyRelations as $r) {
            if (!$r['inEdit']) {
                continue;
            }

            $string .= $this->newLines(1) .
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
                );
        }

        return $string;
    }

    public function getRulesForBelongsToFields()
    {

        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        'required'
                    ],
                    $this->getChildFieldTemplate()
                );
        }
        return $string;
    }

    public function getAttributesForBelongsToFields()
    {

        if (!$this->isBelongsToEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->newLines(1, 2) .
                Str::replace(
                    [
                        '##COLUMN_NAME##',
                        '##VALUE##',
                    ],
                    [
                        $r['foreignKey'],
                        Str::ucfirst($r['relationName']),
                    ],
                    $this->getChildFieldTemplate()
                );
        }
        return $string;
    }

    public function getBelongsToInitCode($isAdd = true)
    {
        if ($isAdd && !$this->isBelongsToAddEnabled()) {
            return '';
        }

        if (!$isAdd && !$this->isBelongsToEditEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->newLines(1) .
                Str::replace(
                    [
                        '##BELONGS_TO_VAR##',
                        '##MODEL##',
                    ],
                    [
                        Str::plural($r['relationName']),
                        $this->getModelName($r['modelPath']),
                    ],
                    $this->getBelongsToInitTemplate()
                );
        }
        return $string;
    }

    public function getBelongsToSaveCode()
    {
        if (!$this->isBelongsToAddEnabled()) {
            return '';
        }

        $string = '';
        foreach ($this->belongsToRelations as $r) {
            $string .= $this->newLines(1, 3) .
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
                );
        }
        return $string;
    }
}
