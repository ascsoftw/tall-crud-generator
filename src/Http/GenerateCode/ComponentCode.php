<?php

namespace Ascsoftw\TallCrudGenerator\Http\GenerateCode;

use Illuminate\Support\Str;

class ComponentCode extends BaseCode
{
    public $tallProperties;

    public function __construct(TallProperties $tallProperties)
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
        if ($this->tallProperties->isSortingEnabled()) {
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
            Template::getSortingVarsTemplate()
        );
    }

    public function getSortingQuery()
    {
        return Template::getSortingQueryTemplate();
    }

    public function getSortingMethod()
    {
        return Template::getSortingMethodTemplate();
    }

    public function getSearchCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->tallProperties->isSearchingEnabled()) {
            $code['vars'] = $this->getSearchVars();
            $code['query'] = $this->getSearchQuery();
            $code['method'] = $this->getSearchMethod();
        }

        return $code;
    }

    public function getSearchVars()
    {
        return Template::getSearchingVarsTemplate();
    }

    public function getSearchQuery()
    {
        $whereClause = $this->getSearchWhereClause();

        return str_replace(
            '##WHERE_CLAUSE##',
            $whereClause->prependAndJoin($this->newLines(1, 6), $this->indent(5)),
            Template::getSearchQueryTemplate()
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
                    Template::getSearchQueryWhereTemplate(),
                )
            );
            $isFirst = false;
        }
        return $whereClause;
    }

    public function getSearchMethod()
    {
        return Template::getSearchMethodTemplate();
    }

    public function getPaginationDropdownCode()
    {
        $code = [
            'method' => '',
        ];
        if ($this->tallProperties->isPaginationDropdownEnabled()) {
            $code['method'] = $this->getPaginationDropdownMethod();
        }

        return $code;
    }

    public function getPaginationDropdownMethod()
    {
        return Template::getPaginationDropdownMethodTemplate();
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
            Template::getPaginationVarsTemplate()
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
            Template::getWithQueryTemplate()
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
            Template::getWithCountQueryTemplate()
        );
    }

    public function getHideColumnsCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
            'method' => '',
        ];
        if ($this->tallProperties->isHideColumnsEnabled()) {
            $code['vars'] = $this->getHideColumnVars();
            $code['init'] = $this->getHideColumnInitCode();
            $code['method'] = $this->getHideColumnMethod();
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
        $columns = $this->tallProperties->getListingColumns();
        $labels = $columns->map(function ($c) {
            return $c['label'];
        });
        return str_replace(
            '##COLUMNS##',
            $this->wrapInQuotesAndJoin($labels),
            Template::getAllColumnsTemplate()
        );
    }

    public function getHideColumnInitCode()
    {
        return Template::getHideColumnInitTemplate();
    }

    public function getBulkActionsCode()
    {
        $code = [
            'vars' => '',
            'method' => '',
        ];
        if ($this->tallProperties->isBulkActionsEnabled()) {
            $code['vars'] = $this->getBulkActionsVars();
            $code['method'] = $this->getBulkActionMethod();
        }

        return $code;
    }

    public function getHideColumnMethod()
    {
        return Template::getHideColumnMethodTemplate();
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
            Template::getBulkActionMethodTemplate()
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
        if ($this->tallProperties->isFilterEnabled()) {
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
                    Template::getSelfFilterInitTemplate()
                )
            );
        }

        if ($filters->isEmpty()) {
            return '';
        }

        return str_replace(
            '##FILTERS##',
            $filters->prependAndJoin($this->newLines(1, 1)) . $this->newLines(1, 2),
            Template::getFilterInitTemplate()
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
                    Template::getKeyLabelTemplate()
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
                Template::getRelationFilterInitTemplate()
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
                Template::getFilterQueryTemplate()
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
                Template::getFilterQueryBtmTemplate()
            );
        })->prependAndJoin($this->newLines());
    }

    public function getFilterMethod()
    {
        return Template::getFilterMethodTemplate();
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
