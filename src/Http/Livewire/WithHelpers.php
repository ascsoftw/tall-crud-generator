<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\Template;

trait WithHelpers
{
    // public function getModelName($name = '')
    // {
    //     if (empty($name)) {
    //         $name = $this->modelPath;
    //     }

    //     return Arr::last(Str::of($name)->explode('\\')->all());
    // }

    public function getPrimaryKey()
    {
        return $this->modelProps['primaryKey'];
    }

    public function getColumns($columns, $primaryKey)
    {
        $collection = collect($columns);

        $filtered = $collection->reject(function ($value) use ($primaryKey) {
            return $value == $primaryKey;
        });

        return $filtered->all();
    }

    public function getDefaultSortableColumn()
    {
        if ($this->isPrimaryKeySortable()) {
            return $this->getPrimaryKey();
        }

        $collection = collect($this->getSortedListingFields());

        $field = $collection->first(function ($f) {
            if ($f['type'] == 'primary') {
                return false;
            }

            if ($f['sortable']) {
                return true;
            }

            return false;
        });

        return $field['column'];
    }

    public function getSearchableColumns()
    {
        $columns = [];
        foreach ($this->fields as $f) {
            if (($this->hasAddAndEditFeaturesDisabled() || $f['inList']) && $f['searchable']) {
                $columns[] = $f;
            }
        }

        return $columns;
    }

    public function validateEmptyColumns()
    {
        $collection = collect($this->fields);

        $filtered = $collection->reject(function ($field) {
            return empty($field['column']);
        });

        return count($this->fields) == count($filtered->all());
    }

    public function validateUniqueFields()
    {
        $collection = collect($this->fields);
        $filtered = $collection->duplicates('column')->all();

        return 0 == count($filtered);
    }

    public function validateEachRow()
    {
        if ($this->hasAddAndEditFeaturesDisabled()) {
            return true;
        }
        foreach ($this->fields as $f) {
            if (! ($f['inList'] ||
                ($this->isAddFeatureEnabled() && $f['inAdd']) ||
                ($this->isEditFeatureEnabled() && $f['inEdit']))) {
                $this->addError('fields', $f['column'].' Column should be selected to display in at least 1 view.');

                return false;
            }
        }

        return true;
    }

    public function validateDisplayColumn()
    {
        if ($this->hasAddAndEditFeaturesDisabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['inList']);
        });

        return 0 != count($filtered->all());
    }

    public function validateCreateColumn()
    {
        if (! $this->isAddFeatureEnabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['inAdd']);
        });

        return 0 != count($filtered->all());
    }

    public function validateEditColumn()
    {
        if (! $this->isEditFeatureEnabled()) {
            return true;
        }

        $collection = collect($this->fields);
        $filtered = $collection->reject(function ($field) {
            return empty($field['inEdit']);
        });

        return 0 != count($filtered->all());
    }

    public function getComponentName()
    {
        return Str::kebab($this->componentName);
    }

    public function getNormalFormFields($addForm = true, $editForm = true)
    {
        if ($this->hasAddAndEditFeaturesDisabled()) {
            return [];
        }

        $columns = [];
        foreach ($this->fields as $f) {
            if (
                ($addForm && $f['inAdd']) ||
                ($editForm && $f['inEdit'])
            ) {
                $columns[] = $f;
            }
        }

        return $columns;
    }

    public function getLabel($label = '', $column = '')
    {
        if (! empty($label)) {
            return $label;
        }

        return Str::title(str_replace('_', ' ', $column));
    }

    public function getLabelForWith($relation = '')
    {
        return Str::ucfirst($relation);
    }

    public function getLabelForWithCount($relation = '')
    {
        return Str::ucfirst($relation).' Count';
    }

    public function getColumnForWithCount($relation = '')
    {
        return $relation.'_count';
    }

    public function getListingFieldsToSort()
    {
        $order = 0;
        $collection = collect();
        if ($this->needsPrimaryKeyInListing()) {
            $collection->push(['field' => $this->getPrimaryKey(), 'type' => 'primary', 'order' => ++$order]);
        }

        foreach ($this->fields as $f) {
            if ($this->hasAddAndEditFeaturesDisabled() || $f['inList']) {
                $collection->push(['field' => $f['column'], 'type' => 'normal', 'order' => ++$order]);
            }
        }

        foreach ($this->withRelations as $r) {
            $collection->push(['field' => $r['relationName'], 'type' => 'with', 'order' => ++$order]);
        }

        foreach ($this->withCountRelations as $r) {
            $collection->push(['field' => $r['relationName'], 'type' => 'withCount', 'order' => ++$order]);
        }

        return $collection->all();
    }

    public function getFormFieldsToSort($addForm = true)
    {
        $collection = collect($this->getNormalFormFields($addForm, ! $addForm));
        $map = $collection->map(function ($item, $key) {
            return ['field' => $item['column'], 'type' => 'normal', 'order' => ++$key];
        });
        //
        $order = $map->count();
        foreach ($this->belongsToManyRelations as $r) {
            if ($addForm && ! $r['inAdd']) {
                continue;
            }

            if (! $addForm && ! $r['inEdit']) {
                continue;
            }
            $map->push(['field' => $r['relationName'], 'type' => 'btm', 'order' => ++$order]);
        }

        foreach ($this->belongsToRelations as $r) {
            if ($addForm && ! $r['inAdd']) {
                continue;
            }

            if (! $addForm && ! $r['inEdit']) {
                continue;
            }
            $map->push(['field' => $r['relationName'], 'type' => 'belongsTo', 'order' => ++$order]);
        }

        return $map->all();
    }

    public function sortFieldsByOrder($fields)
    {
        if (is_array($fields)) {
            $fields = collect($fields);
        }
        $sorted = $fields->sortBy('order');

        return $sorted->values()->all();
    }

    public function getSortedFormFields($addForm = true)
    {
        $sortFields = collect($this->sortFieldsByOrder($this->sortFields[$addForm ? 'add' : 'edit']));
        $collection = collect($this->getNormalFormFields($addForm, ! $addForm));
        $btmCollection = $this->getBtmCollection($addForm);
        $belongsToCollection = $this->getBelongsToCollection($addForm);

        return $sortFields->map(function ($i) use ($collection, $btmCollection, $belongsToCollection) {
            switch ($i['type']) {
                case 'normal':
                    return collect(
                        $collection->firstWhere(
                            'column',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'normal'])
                        ->all();
                case 'btm':
                    return collect(
                        $btmCollection->firstWhere(
                            'relationName',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'btm'])
                        ->all();
                case 'belongsTo':
                    return collect(
                        $belongsToCollection->firstWhere(
                            'relationName',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'belongsTo'])
                        ->all();
            }

            return $collection->firstWhere('column', $i['field']);
        });
    }

    public function getSortedListingFields()
    {
        $sortFields = collect($this->sortFieldsByOrder($this->sortFields['listing']));
        $collection = collect($this->fields);
        $withRelationsCollection = collect($this->withRelations);
        $withCountRelationsCollection = collect($this->withCountRelations);

        return $sortFields->map(function ($i) use ($collection, $withRelationsCollection, $withCountRelationsCollection) {
            switch ($i['type']) {
                case 'primary':
                    return ['field' => $i['field'], 'type' => $i['type']];
                case 'normal':
                    return collect(
                        $collection->firstWhere(
                            'column',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'normal'])
                        ->all();
                case 'with':
                    return collect(
                        $withRelationsCollection->firstWhere(
                            'relationName',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'with'])
                        ->all();
                case 'withCount':
                    return collect(
                        $withCountRelationsCollection->firstWhere(
                            'relationName',
                            $i['field']
                        )
                    )
                        ->merge(['type' => 'withCount'])
                        ->all();
            }
        });
    }

    public function isBelongsToManyRelation($relation)
    {
        if (empty($this->allRelations['belongsToMany'])) {
            return false;
        }
        foreach ($this->allRelations['belongsToMany'] as $k) {
            if ($k['name'] == $relation) {
                return true;
            }
        }

        return false;
    }

    public function isHasManyRelation($relation)
    {
        if (empty($this->allRelations['hasMany'])) {
            return false;
        }

        foreach ($this->allRelations['hasMany'] as $k) {
            if ($k['name'] == $relation) {
                return true;
            }
        }

        return false;
    }

    public function getBtmCollection($addForm)
    {
        $btmCollection = collect();
        foreach ($this->belongsToManyRelations as $r) {
            if ($addForm && ! $r['inAdd']) {
                continue;
            }

            if (! $addForm && ! $r['inEdit']) {
                continue;
            }
            $btmCollection->push($r);
        }

        return $btmCollection;
    }

    public function getBelongsToCollection($addForm)
    {
        $belongsToCollection = collect();
        foreach ($this->belongsToRelations as $r) {
            if ($addForm && ! $r['inAdd']) {
                continue;
            }

            if (! $addForm && ! $r['inEdit']) {
                continue;
            }
            $belongsToCollection->push($r);
        }

        return $belongsToCollection;
    }

    public function getListingColumns()
    {
        $fields = $this->getSortedListingFields();
        $headers = collect();

        foreach ($fields as $f) {
            $headers->push($this->getTableColumnProps($f));
        }
        return $headers;
    }

    public function getTableColumnProps($field)
    {
        switch ($field['type']) {
            case 'primary':
                $label = $this->getLabel($this->primaryKeyProps['label'], $this->getPrimaryKey());
                $column = $this->getPrimaryKey();
                $isSortable = $this->isPrimaryKeySortable();
                $slot = $this->getPrimaryKey();

                break;
            case 'normal':
                $label = $this->getLabel($field['label'], $field['column']);
                $column = $field['column'];
                $isSortable = $this->isColumnSortable($field['column']);
                $slot = $field['column'];

                break;
            case 'with':
                $label = $this->getLabelForWith($field['relationName']);
                $column = null;
                $isSortable = false;
                $slot = $this->getWithTableSlot($field);

                break;
            case 'withCount':
                $label = $this->getLabelForWithCount($field['relationName']);
                $column = $this->getColumnForWithCount($field['relationName']);
                $isSortable = $field['isSortable'];
                $slot = $this->getColumnForWithCount($field['relationName']);

                break;
        }

        return ['label' => $label, 'column' => $column, 'isSortable' => $isSortable, 'slot' => $slot];
    }

    public function getWithTableSlot($r)
    {
        if ($this->isBelongsToManyRelation($r['relationName']) || $this->isHasManyRelation($r['relationName'])) {
            return str_replace(
                [
                    '##RELATION##',
                    '##DISPLAY_COLUMN##',
                ],
                [
                    $r['relationName'],
                    $r['displayColumn'],
                ],
                Template::getBelongsToManyTableSlotTemplate()
            );
        }

        return str_replace(
            [
                '##RELATION##',
                '##DISPLAY_COLUMN##',
            ],
            [
                $r['relationName'],
                $r['displayColumn'],
            ],
            Template::getBelongsToTableSlotTemplate()
        );
    }
}
