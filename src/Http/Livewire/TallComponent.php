<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

class TallComponent 
{
    public $otherModels;

    public $sorting;
    public $defaultSortableColumn;

    public $searching;
    public $searchableColumns;

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

    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }
    
    public function getSorting()
    {
        return $this->sorting;
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
        if ($this->getSorting()) {
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

    public function setSearching($searching)
    {
        $this->searching = $searching;
    }
    
    public function getSearching()
    {
        return $this->searching;
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
        if ($this->getSearching()) {
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