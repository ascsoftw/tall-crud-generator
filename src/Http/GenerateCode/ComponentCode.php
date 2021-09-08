<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;
use Illuminate\Support\Str;

class ComponentCode extends BaseCode
{
    use WithTemplates;

    public $tallProperties;

    public function __construct($tallProperties)
    {
        $this->tallProperties = $tallProperties;
    }

    public function getOtherModelsCode()
    {
        $models = $this->tallProperties->getOtherModels();
        return $models->map(function ($m) {
            return $this->getUseModelCode($m);
        })->implode('');
    }

    public function getSortCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getSortingFlag()) {
            $code['vars'] = $this->getSortingVars();
            $code['query'] = $this->getSortingQuery();
            $code['method'] = $this->getSortingMethod();
        }

        return $code;
    }

    public function getSortingVars()
    {
        return str_replace(
            '##SORT_COLUMN##',
            $this->tallProperties->getDefaultSortableColumn(),
            WithTemplates::getSortingVarsTemplate()
        );
    }

    public function getSortingQuery()
    {
        return WithTemplates::getSortingQueryTemplate();
    }

    public function getSortingMethod()
    {
        return WithTemplates::getSortingMethodTemplate();
    }

    public function getSearchCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getSearchingFlag()) {
            $code['vars'] = $this->getSearchVars();
            $code['query'] = $this->getSearchQuery();
            $code['method'] = $this->getSearchMethod();
        }

        return $code;
    }

    public function getSearchVars()
    {
        return WithTemplates::getSearchingVarsTemplate();
    }

    public function getSearchQuery()
    {
        $whereClause = $this->getSearchWhereClause();

        return str_replace(
            '##WHERE_CLAUSE##',
            $whereClause->prependAndJoin($this->newLines(1, 6), $this->indent(5)),
            WithTemplates::getSearchQueryTemplate()
        );
    }

    public function getSearchWhereClause()
    {
        $whereClause = collect();
        $searchableColumns = $this->tallProperties->getSearchableColumns();
        $isFirst = true;
        foreach ($searchableColumns as $column) {
            $whereClause->push(
                str_replace(
                    [
                        '##QUERY##',
                        '##COLUMN##',
                    ],
                    [
                        $isFirst ? '$query->where' : '->orWhere',
                        $column,
                    ],
                    WithTemplates::getSearchQueryWhereTemplate(),
                )
            );
            $isFirst = false;
        }
        return $whereClause;
    }

    public function getSearchMethod()
    {
        return WithTemplates::getSearchMethodTemplate();
    }

    public function getPaginationDropdownCode()
    {
        $code = [
            'method' => '',
        ];
        if ($this->tallProperties->getPaginationDropdownFlag()) {
            $code['method'] = $this->getPaginationDropdownMethod();
        }

        return $code;
    }

    public function getPaginationDropdownMethod()
    {
        return WithTemplates::getPaginationDropdownMethodTemplate();
    }

    public function getPaginationCode()
    {
        $code = [
            'vars' => '',
        ];

        $code['vars'] = $this->getPaginationVars();

        return $code;
    }

    public function getPaginationVars()
    {
        return str_replace(
            '##PER_PAGE##',
            $this->tallProperties->getRecordsPerPage(),
            WithTemplates::getPaginationVarsTemplate()
        );
    }

    public function getWithQueryCode()
    {
        $models = $this->tallProperties->getEagerLoadModels();
        if ($models->isEmpty()) {
            return '';
        }

        return str_replace(
            '##RELATIONS##',
            $this->wrapInQuotesAndJoin($models),
            WithTemplates::getWithQueryTemplate()
        );
    }

    public function getWithCountQueryCode()
    {
        $models = $this->tallProperties->getEagerLoadCountModels();
        if ($models->isEmpty()) {
            return '';
        }

        return str_replace(
            '##RELATIONS##',
            $this->wrapInQuotesAndJoin($models),
            WithTemplates::getWithCountQueryTemplate()
        );
    }

    public function getHideColumnsCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
        ];
        if ($this->tallProperties->getHideColumnsFlag()) {
            $code['vars'] = $this->getHideColumnVars();
            $code['init'] = $this->getHideColumnInitCode();
        }

        return $code;
    }

    public function getHideColumnVars()
    {
        return $this->getAllColumnsVars() .
            $this->newLines() .
            self::getEmtpyArray('selectedColumns');
    }

    public function getAllColumnsVars()
    {
        return str_replace(
            '##COLUMNS##',
            $this->wrapInQuotesAndJoin($this->tallProperties->getListingColumns()),
            WithTemplates::getAllColumnsTemplate()
        );
    }

    public function getHideColumnInitCode()
    {
        return WithTemplates::getHideColumnInitTemplate();
    }

    public function getBulkActionsCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getBulkActionFlag()) {
            $code['vars'] = $this->getBulkActionsVars();
            $code['method'] = $this->getBulkActionMethod();
        }

        return $code;
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
                $this->tallProperties->getModelName(),
                $this->tallProperties->getPrimaryKey(),
                $this->tallProperties->getBulkActionColumn(),
            ],
            WithTemplates::getBulkActionMethodTemplate()
        );
    }

    public function getBulkActionsVars()
    {
        return $this->newLines() .
            self::getEmtpyArray('selectedItems');
    }

    public function getFilterCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->tallProperties->getFilterFlag()) {
            $code['vars'] = $this->getFilterVars();
            $code['init'] = $this->getFilterInitCode();
            $code['query'] = $this->getFilterQuery();
            $code['method'] = $this->getFilterMethod();
        }
        return $code;
    }

    public function getFilterVars()
    {
        $vars = collect();
        $vars->push(self::getEmtpyArray('filters'));
        $vars->push(self::getEmtpyArray('selectedFilters'));
        return $vars->prependAndJoin($this->newLines());
    }

    public function getFilterInitCode()
    {
        return $this->getSelfFilterInitCode() .
            $this->getRelationFilterInitCode();
    }

    public function getSelfFilterInitCode()
    {
        $filters = collect();
        foreach ($this->tallProperties->selfFilters as $f) {
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
                    WithTemplates::getSelfFilterInitTemplate()
                )
            );
        }

        if ($filters->isEmpty()) {
            return '';
        }

        return str_replace(
            '##FILTERS##',
            $filters->prependAndJoin($this->newLines(1, 1)) . $this->newLines(1, 2),
            WithTemplates::getFilterInitTemplate()
        );
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
                    WithTemplates::getKeyLabelTemplate()
                )
            );
        }

        return $filterOptions;
    }

    public function getRelationFilterInitCode()
    {
        $filters = $this->tallProperties->btmFilters->merge($this->tallProperties->belongsToFilters);

        return $filters->map(function ($f) {
            return str_replace(
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
                    $this->tallProperties->getModelName($f['modelPath']),
                    $f['column'],
                    $this->getFilterOwnerKey($f),
                    $this->getFilterForeignKey($f),
                    $this->getFilterLabelName($f),
                ],
                WithTemplates::getRelationFilterInitTemplate()
            );
        })->implode('');
    }

    public function getFilterQuery()
    {
        return $this->getSelfFilterQuery() . $this->getBtmFilterQuery();
    }

    public function getSelfFilterQuery()
    {
        $filters = $this->tallProperties->selfFilters->merge($this->tallProperties->belongsToFilters);

        return $filters->map(function ($f) {
            return str_replace(
                '##COLUMN##',
                $this->getFilterColumnName($f),
                WithTemplates::getFilterQueryTemplate()
            );
        })->prependAndJoin($this->newLines());
    }

    public function getBtmFilterQuery()
    {
        $filters = $this->tallProperties->btmFilters;

        return $filters->map(function ($f) {
            return str_replace(
                [
                    '##COLUMN##',
                    '##RELATION##',
                    '##RELATED_KEY##',
                    '##TABLE##',
                ],
                [
                    $f['relation'] . '_' . $f['relatedKey'],
                    $f['relation'],
                    $f['relatedKey'],
                    $f['relatedTableName'],
                ],
                WithTemplates::getFilterQueryBtmTemplate()
            );
        })->prependAndJoin($this->newLines());
    }

    public function getFilterMethod()
    {
        return WithTemplates::getFilterMethodTemplate();
    }

    public function getFilterColumnName($filter)
    {
        return ($filter['type'] == 'None') ? $filter['column'] : $filter['foreignKey'];
    }

    public function getFilterLabelName($filter)
    {
        if ($filter['type'] == 'None') {
            return Str::ucfirst($filter['column']);
        }

        return Str::ucfirst($filter['relation']);
    }

    public function getFilterOwnerKey($filter)
    {
        if ($filter['type'] == 'BelongsTo') {
            return $filter['ownerKey'];
        }

        return $filter['relatedKey'];
    }

    public function getFilterForeignKey($filter)
    {
        if ($filter['type'] == 'BelongsTo') {
            return $filter['foreignKey'];
        }

        return $filter['relation'] . '_' . $filter['relatedKey'];
    }
}
