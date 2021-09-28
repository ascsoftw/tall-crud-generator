<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class TallCrudGenerator extends Component
{
    use WithFeatures;
    use WithHelpers;
    use WithViewCode;
    use WithComponentCode;
    use WithRelations;
    use WithFilters;

    public $totalSteps = 7;
    public $step = 1;
    public $selected = null;

    public $exitCode;
    public $isComplete = false;
    public $generatedCode;

    public $modelPath = '';
    public $isValidModel = false;
    public $modelProps = [];

    public $fields = [];
    public $sortFields = [];

    public $attributeKey;
    public $confirmingAttributes = false;
    public $attributes = [
        'rules' => '',
        'type' => 'input',
        'options' => '',
    ];

    public $componentName = '';
    public $componentProps = [
        'createAddModal' => true,
        'createEditModal' => true,
        'createDeleteButton' => true,
    ];

    public $primaryKeyProps = [
        'inList' => true,
        'label' => '',
        'sortable' => true,
    ];

    public $advancedSettings = [
        'text' => [
            'title' => '',
            'createButton' => 'Save',
            'editButton' => 'Save',
            'cancelButton' => 'Cancel',
            'deleteButton' => 'Delete',
        ],
        'table_settings' => [
            'showPaginationDropdown' => true,
            'recordsPerPage' => 15,
            'showHideColumns' => false,
            'bulkActions' => false,
            'bulkActionColumn' => '',
            'classes' => [
                'th' => 'text-left font-bold bg-blue-400',
                'trHover' => 'bg-blue-300',
                'trEven' => 'bg-blue-100',
                'trBottomBorder' => 'blue-400',
                'td' => 'px-3 py-2',
            ],
        ],
    ];

    public $flashMessages = [
        'enable' => true,
        'text' => [
            'add' => 'Record Added Successfully',
            'edit' => 'Record Updated Successfully',
            'delete' => 'Record Deleted Successfully',
        ],
    ];

    public $showAdvanced = false;

    protected $rules = [
        'modelPath' => 'required',
        'componentName' => 'required|alpha_dash|min:3',
    ];

    protected $messages = [
        'modelPath.required' => 'Please enter Path to your Model',
        'componentName.required' => 'Please enter the name of your component',
        'componentName.alpha_dash' => 'Only alphanumeric, dashes and underscore are allowed',
        'componentName.min' => 'Must be minimum of 3 characters',
        'belongsToManyRelation.displayColumn.required' => 'Please select a value.',
        'belongsToRelation.displayColumn.required' => 'Please select a value.',
        'withRelation.displayColumn.required' => 'Please select a value.',
        'filter.type.required' => 'Please select a value.',
        'filter.relation.required' => 'Please select a value.',
        'filter.column.required' => 'Please select a value.',
    ];

    public $confirmingSorting = false;
    public $sortingMode = '';

    public function render()
    {
        return view('tall-crud-generator::livewire.tall-crud-generator');
    }

    public function moveAhead()
    {
        switch ($this->step) {
            case 1:
                //Validate Model
                $this->checkModel();
                if (! $this->isValidModel) {
                    return;
                }

                break;
            case 2:
                //Validate Features
                break;
            case 3:
                //Validate Fields
                if (! $this->validateSettings()) {
                    return;
                }

                //Prepare for Next Step.
                $this->getAllRelations();

                break;
            case 4:
                //Relation Fields

                //Prepare for Next Step.
                $this->getSortFields();
                $this->confirmingSorting = false;

                break;
            case 5:
                //Validate Sort Fields
                //Prepare for Next Step.
                $this->resetFilter();

                break;
            case 6:
                //Validate Advanced Section
                //Prepare for Next Step.
                $this->isComplete = false;

                break;
            case 7:
                //Validate Generate Files
                $this->validateOnly('componentName');
                $this->generateFiles();

                return;

                break;
        }

        $this->resetErrorBag();

        //Increase Step
        $this->step += 1;
        $this->validateStep();
        $this->selected = null;
    }

    public function moveBack()
    {
        $this->step -= 1;
        $this->selected = null;
        $this->validateStep();
    }

    public function validateStep()
    {
        if ($this->step < 1) {
            $this->step = 1;
        }

        if ($this->step > $this->totalSteps) {
            $this->step = $this->totalSteps;
        }

        if ($this->step > 1 && ! $this->isValidModel) {
            $this->step = 1;
        }
    }

    public function checkModel()
    {
        if ($this->isValidModel) {
            return;
        }

        $this->validateOnly('modelPath');

        //check class exists
        $this->resetValidation();
        if (! class_exists($this->modelPath)) {
            $this->addError('modelPath', 'File does not exists');

            return;
        }

        try {
            $model = new $this->modelPath();
            $this->modelProps['tableName'] = $model->getTable();
            $this->modelProps['primaryKey'] = $model->getKeyName();
            $this->modelProps['columns'] = $this->getColumns(Schema::getColumnListing($model->getTable()), $this->modelProps['primaryKey']);
        } catch (Exception $e) {
            $this->addError('modelPath', 'Not a Valid Model Class.');

            return;
        }

        $this->isValidModel = true;
        $this->advancedSettings['text']['title'] = Str::title($this->modelProps['tableName']);
    }

    public function addField($column = '')
    {
        $this->fields[] = [
            'column' => $column,
            'label' => '',
            'sortable' => false,
            'searchable' => false,
            'inList' => true,
            'inAdd' => true,
            'inEdit' => true,
            'attributes' => [
                'rules' => '',
                'type' => 'input',
                'options' => '{"1" : "Yes", "0": "No"}',
            ],
        ];
        $this->resetValidation('fields');
    }

    public function deleteField($i)
    {
        unset($this->fields[$i]);
        $this->fields = array_values($this->fields);
        $this->resetValidation('fields');
    }

    public function addAllFields()
    {
        foreach ($this->modelProps['columns'] as $column) {
            $this->addField($column);
        }
    }

    public function showAttributes($i)
    {
        $this->confirmingAttributes = true;
        $this->attributes = $this->fields[$i]['attributes'];
        $this->attributeKey = $i;
    }

    public function addRule($rule)
    {
        $this->attributes['rules'] .= $rule.',';
    }

    public function clearRules()
    {
        $this->attributes['rules'] = '';
    }

    public function setAttributes()
    {
        $this->fields[$this->attributeKey]['attributes'] = $this->attributes;
        $this->confirmingAttributes = false;
        $this->attributeKey = false;
    }

    public function validateSettings()
    {
        $this->resetValidation('fields');

        if (empty($this->fields)) {
            $this->addError('fields', 'At least 1 Field should be added.');

            return;
        }

        if (! $this->validateEmptyColumns()) {
            $this->addError('fields', 'Please select column for all fields.');

            return;
        }

        if (! $this->validateUniqueFields()) {
            $this->addError('fields', 'Please do not select a column more than once.');

            return false;
        }

        if (! $this->validateEachRow()) {
            return false;
        }

        if (! $this->validateDisplayColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Listing Column.');

            return false;
        }

        if (! $this->validateCreateColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Create Column.');

            return false;
        }

        if (! $this->validateEditColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Edit Column.');

            return false;
        }

        return true;
    }

    public function getSortFields()
    {
        $this->sortFields['listing'] = $this->getListingFieldsToSort();
        $this->sortFields['add'] = $this->getFormFieldsToSort(true);
        $this->sortFields['edit'] = $this->getFormFieldsToSort(false);
    }

    public function showSortDialog($mode)
    {
        $this->confirmingSorting = true;
        $this->sortingMode = $mode;
        $this->dispatchBrowserEvent('init-sort-events');
    }

    public function hideSortDialog()
    {
        $this->confirmingSorting = false;
        $this->sortingMode = '';
    }

    public function reorder($order)
    {
        $mode = $this->sortingMode;
        $collection = collect($this->sortFields[$mode]);
        $orderCollection = collect($order);
        $map = $collection->map(function ($item) use ($orderCollection) {
            $searchTerm = $item['type'] == 'withCount' ? $item['field'].' (Count)' : $item['field'];
            $item['order'] = $orderCollection->search($searchTerm) + 1;

            return $item;
        });
        $this->sortFields[$mode] = $this->sortFieldsByOrder($map);
    }

    public function showHideAccordion($selected)
    {
        if ($this->selected == $selected) {
            $this->selected = null;
        } else {
            $this->selected = $selected;
        }
    }

    public function generateFiles()
    {
        $code = $this->generateComponentCode();
        $html = $this->generateViewHtml();
        $this->props = [
            'modelPath' => $this->modelPath,
            // 'model' => $this->getModelName(),
            'model' => $this->tallProperties->getModelName(),
            'modelProps' => $this->modelProps,
            'fields' => $this->fields,
            'componentProps' => $this->componentProps,
            'primaryKeyProps' => $this->primaryKeyProps,
            'advancedSettings' => $this->advancedSettings,
            'html' => $html,
            'code' => $code,
        ];

        $this->exitCode = Artisan::call('livewire:tall-crud-generator', [
            'name' => $this->componentName,
            'props' => $this->props,
            '--child' => false,
        ]);

        if ($this->exitCode == 0) {
            if ($this->isAddFeatureEnabled() || $this->isEditFeatureEnabled() || $this->isDeleteFeatureEnabled()) {
                $this->exitCode = Artisan::call('livewire:tall-crud-generator', [
                    'name' => $this->componentName.'Child',
                    'props' => $this->props,
                    '--child' => true,
                ]);
            }
        }

        $this->generatedCode = "@livewire('".$this->getComponentName()."')";
        $this->isComplete = true;
    }

    //Define all ComputedProperties.
    public function getAddFeatureProperty()
    {
        return $this->isAddFeatureEnabled();
    }

    public function getEditFeatureProperty()
    {
        return $this->isEditFeatureEnabled();
    }

    public function getAddAndEditDisabledProperty()
    {
        return $this->hasAddAndEditFeaturesDisabled();
    }

    public function getAdvancedSettingLabel($key)
    {
        //Str::replace does not exist in pre v8.41.0
        return Str::title(str_replace('-', ' ', Str::kebab($key)));
    }
}
