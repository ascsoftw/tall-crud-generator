<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Str;

class TallComponent 
{
    public $otherModels;

    public $sortingFlag;
    public $defaultSortableColumn;

    public $searchingFlag;
    public $searchableColumns;

    public $paginationDropdownFlag;

    public $recordsPerPage;

    public $eagerLoadModels;

    public $eagerLoadCountModels;

    public $hideColumnsFlag;
    public $listingColumns;

    public function setOtherModels($filters)
    {
        $this->otherModels = collect();
        foreach ($filters as $f) {
            if ($f['type'] == 'None') {
                continue;
            }
            $this->otherModels->push($f['modelPath']);
        }

        $this->otherModels->unique();
    }

    public function getOtherModels()
    {
        return $this->otherModels;
    }

    public function getOtherModelsCode()
    {
        $models = $this->getOtherModels();
        $modelsCode = collect();
        foreach ($models as $model) {
            $modelsCode->push($this->getUseModelCode($model));
        }

        return $modelsCode->implode('');
    }

    public function getUseModelCode($modelPath)
    {
        return str_replace(
            '##MODEL##',
            $modelPath,
            WithTemplates::getUseModelTemplate()
        );
    }

    public function setSortingFlag($sortingFlag)
    {
        $this->sortingFlag = $sortingFlag;
    }
    
    public function getSortingFlag()
    {
        return $this->sortingFlag;
    }

    public function setDefaultSortableColumn($defaultSortableColumn)
    {
        $this->defaultSortableColumn = $defaultSortableColumn;
    }

    public function getDefaultSortableColumn()
    {
        return $this->defaultSortableColumn;
    }

    public function getSortCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->getSortingFlag()) {
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
            $this->getDefaultSortableColumn(),
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

    public function setSearchingFlag($searchingFlag)
    {
        $this->searchingFlag = $searchingFlag;
    }
    
    public function getSearchingFlag()
    {
        return $this->searchingFlag;
    }

    public function setSearchableColumns($columns)
    {
        $this->searchableColumns = collect();
        foreach($columns as $column)
        {
            $this->searchableColumns->push($column['column']);
        }
    }
    
    public function getSearchableColumns()
    {
        return $this->searchableColumns;
    }

    public function getSearchCode()
    {
        $code = [
            'vars' => '',
            'query' => '',
            'method' => '',
        ];
        if ($this->getSearchingFlag()) {
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
        $searchableColumns = $this->getSearchableColumns();
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

    public function setPaginationDropdownFlag($paginationDropdownFlag)
    {
        $this->paginationDropdownFlag = $paginationDropdownFlag;
    }
    
    public function getPaginationDropdownFlag()
    {
        return $this->paginationDropdownFlag;
    }

    public function getPaginationDropdownCode()
    {
        $code = [
            'method' => '',
        ];
        if ($this->getPaginationDropdownFlag()) {
            $code['method'] = $this->getPaginationDropdownMethod();
        }

        return $code;
    }

    public function getPaginationDropdownMethod()
    {
        return WithTemplates::getPaginationDropdownMethodTemplate();
    }

    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }
    
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
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
            $this->getRecordsPerPage(),
            WithTemplates::getPaginationVarsTemplate()
        );
    }

    public function setEagerLoadModels($relations)
    {
        $this->eagerLoadModels = collect();
        foreach ($relations as $r) {
            $this->eagerLoadModels->push($r['relationName']);
        }
    }
    
    public function getEagerLoadModels()
    {
        return $this->eagerLoadModels;
    }

    public function getWithQueryCode()
    {
        $models = $this->getEagerLoadModels();
        if ($models->isEmpty()) {
            return '';
        }

        return str_replace(
            '##RELATIONS##',
            $this->wrapInQuotesAndJoin($models),
            WithTemplates::getWithQueryTemplate()
        );
    }

    public function setEagerLoadCountModels($relations)
    {
        $this->eagerLoadCountModels = collect();
        foreach ($relations as $r) {
            $this->eagerLoadCountModels->push($r['relationName']);
        }
    }
    
    public function getEagerLoadCountModels()
    {
        return $this->eagerLoadCountModels;
    }

    public function getWithCountQueryCode()
    {
        $models = $this->getEagerLoadCountModels();
        if ($models->isEmpty()) {
            return '';
        }

        return str_replace(
            '##RELATIONS##',
            $this->wrapInQuotesAndJoin($models),
            WithTemplates::getWithCountQueryTemplate()
        );
    }

    public function setHideColumnsFlag($hideColumnsFlag)
    {
        $this->hideColumnsFlag = $hideColumnsFlag;
    }
    
    public function getHideColumnsFlag()
    {
        return $this->hideColumnsFlag;
    }

    public function setListingColumns($listingColumns)
    {
        $this->listingColumns = $listingColumns;
    }
    
    public function getListingColumns()
    {
        return $this->listingColumns;
    }

    public function getHideColumnsCode()
    {
        $code = [
            'vars' => '',
            'init' => '',
        ];
        if ($this->getHideColumnsFlag()) {
            $code['vars'] = $this->getHideColumnVars();
            $code['init'] = $this->getHideColumnInitCode();
        }

        return $code;
    }

    public function getHideColumnVars()
    {
        return $this->getAllColumnsVars().
            $this->newLines().
            self::getEmtpyArray('selectedColumns');
    }

    public function getAllColumnsVars()
    {
        return str_replace(
            '##COLUMNS##',
            $this->wrapInQuotesAndJoin($this->getListingColumns()),
            WithTemplates::getAllColumnsTemplate()
        );
    }

    public function getHideColumnInitCode()
    {
        return WithTemplates::getHideColumnInitTemplate();
    }

    public function wrapInQuotesAndJoin($collection, $glue = ',')
    {
        return $collection->map(function ($m) {
            return Str::of($m)->append("'")->prepend("'");
        })->implode($glue);
    }

    public static function getEmtpyArray($name, $type = 'array')
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
            WithTemplates::getEmptyArrayTemplate()
        );
    }

    public function newLines($count = 1, $indent = 0)
    {
        return str_repeat("\n".$this->indent($indent), $count);
    }

    public function spaces($count = 1)
    {
        return str_repeat(' ', $count);
    }

    public function indent($step = 1)
    {
        return $this->spaces($step * 4);
    }
}