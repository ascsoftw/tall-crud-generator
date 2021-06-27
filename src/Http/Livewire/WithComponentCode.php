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

        $return['child_delete'] = $this->_generateDeleteCode();
        $return['child_add'] = $this->_generateAddCode();
        $return['child_edit'] = $this->_generateEditCode();
        $return['child_listeners'] = $this->_generateChildListeners();
        $return['child_item'] = $this->_generateChildItem();
        $return['child_rules'] = $this->_generateChildRules();
        $return['child_validation_attributes'] = $this->_generateChildValidationAttributes();
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
            ],
            [
                $this->_getModelName(),
                $this->componentName
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
                    '##COLUMN##',
                    $field['column'],
                    $this->_getCreateFieldTemplate()
                );
        }

        return Str::replace(
            [
                '##MODEL##',
                '##COMPONENT_NAME##',
                '##CREATE_FIELDS##'
            ],
            [
                $this->_getModelName(),
                $this->componentName,
                $string,
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
                '##COMPONENT_NAME##',
            ],
            [
                $this->_getModelName(),
                $this->componentName
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
        return Str::replace('##ATTRIBUTES##', $string, $this->_getchildValidationAttributesTemplate());
    }
}
