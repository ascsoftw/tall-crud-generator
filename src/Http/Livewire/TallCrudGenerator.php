<?php

namespace Ascsoftw\TallCrudGenerator\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Exception;

class TallCrudGenerator extends Component
{
    use WithFeatures;
    use WithHelpers;
    use WithBaseHtml;
    use WithViewCode;
    use WithComponentCode;
    use WithTemplates;
    use WithRelations;

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
        'options' => ''
    ];

    public $componentName = '';
    public $componentProps = [
        'create_add_modal' => true,
        'create_edit_modal' => true,
        'create_delete_button' => true,
    ];

    public $primaryKeyProps = [
        'in_list' => true,
        'label' => '',
        'sortable' => true,
    ];

    public $advancedSettings = [
        'title' => '',
        'text' => [
            'add_link' => 'Create New',
            'edit_link' => 'Edit',
            'delete_link' => 'Delete',
            'create_button' => 'Save',
            'edit_button' => 'Save',
            'cancel_button' => 'Cancel',
            'delete_button' => 'Delete',
        ],
        'table_settings' => [
            'show_pagination_dropdown' => true,
            'records_per_page' => 15,
        ]
    ];

    public $flashMessages = [
        'enable' => 'true',
        'text' => [
            'add' => 'Record Added Successfully',
            'edit' => 'Record Updated Successfully',
            'delete' => 'Record Deleted Successfully',
        ]
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
    ];

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
                if (!$this->isValidModel) {
                    return;
                }
                break;
            case 2:
                //Validate Features
                break;
            case 3:
                //Validate Fields
                if (!$this->validateSettings()) {
                    return;
                }

                //Prepare for Next Step.
                $this->getAllRelations();
                break;
            case 4:
                //Relation Fields

                //Prepare for Next Step.
                $this->_getSortFields();
                break;
            case 5:
                //Validate Sort Fields
                break;
            case 6:
                //Validate Advanced Section
                //Prepare for Next Step.
                $this->isComplete = false;
                break;
            case 7:
                //Validate Generate Files
                $this->validateOnly('componentName');
                $this->_generateFiles();
                return;
                break;
        }

        $this->resetErrorBag();

        //Increase Step
        $this->step += 1;
        $this->_validateStep();
        $this->selected = null;
    }

    public function moveBack()
    {
        $this->step -= 1;
        $this->_validateStep();
    }

    private function _validateStep()
    {
        if ($this->step < 1) {
            $this->step = 1;
        }

        if ($this->step > $this->totalSteps) {
            $this->step = $this->totalSteps;
        }

        if ($this->step > 1 && !$this->isValidModel) {
            $this->stp = 1;
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
        if (!class_exists($this->modelPath)) {
            $this->addError('modelPath', 'File does not exists');
            return;
        }

        try {
            $model = new $this->modelPath();
            $this->modelProps['table_name'] = $model->getTable();
            $this->modelProps['primary_key'] = $model->getKeyName();
            $this->modelProps['columns'] = $this->_getColumns(Schema::getColumnListing($model->getTable()), $this->modelProps['primary_key']);
        } catch (Exception $e) {
            $this->addError('modelPath', 'Not a Valid Model Class.');
            return;
        }

        $this->isValidModel = true;
        $this->advancedSettings['title'] = Str::title($this->modelProps['table_name']);
    }

    public function addField()
    {
        $this->fields[] = [
            'column' => '',
            'label' => '',
            'sortable' => false,
            'searchable' => false,
            'in_list' => true,
            'in_add' => true,
            'in_edit' => true,
            'attributes' => [
                'rules' => '',
                'type' => 'input',
                'options' => '{"1" : "Yes", "0": "No"}'
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

    public function showAttributes($i)
    {
        $this->confirmingAttributes = true;
        $this->attributes = $this->fields[$i]['attributes'];
        $this->attributeKey = $i;
    }

    public function addRule($rule)
    {
        $this->attributes['rules'] .= $rule . ',';
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

        if (!$this->_validateEmptyColumns()) {
            $this->addError('fields', 'Please select column for all fields.');
            return;
        }

        if (!$this->_validateUniqueFields()) {
            $this->addError('fields', 'Please do not select a column more than once.');
            return false;
        }

        if (!$this->_validateEachRow()) {
            return false;
        }

        if (!$this->_validateDisplayColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Listing Column.');
            return false;
        }

        if (!$this->_validateCreateColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Create Column.');
            return false;
        }

        if (!$this->_validateEditColumn()) {
            $this->addError('fields', 'Please select at least 1 Field to Display in Edit Column.');
            return false;
        }

        return true;
    }

    private function _getSortFields()
    {
        $this->sortFields['listing'] = $this->_getListingFieldsToSort();
        $this->sortFields['add'] = $this->_getFormFieldsToSort(true);
        $this->sortFields['edit'] = $this->_getFormFieldsToSort(false);
    }

    public function moveUp($field, $mode)
    {
        $collection = collect($this->sortFields[$mode]);
        $f = $collection->firstWhere('field', $field);
        $findOrder = $f['order'] - 1;

        $map = $collection->map(function ($item) use ($findOrder, $field) {
            if ($item['order'] == $findOrder) {
                $item['order']++;
                return $item;
            }

            if ($item['field'] == $field) {
                $item['order']--;
                return $item;
            }

            return $item;
        });

        $this->sortFields[$mode] = $this->_sortFieldsByOrder($map);
    }

    public function moveDown($field, $mode)
    {
        $collection = collect($this->sortFields[$mode]);
        $f = $collection->firstWhere('field', $field);
        $findOrder = $f['order'] + 1;

        $map = $collection->map(function ($item) use ($findOrder, $field) {
            if ($item['order'] == $findOrder) {
                $item['order']--;
                return $item;
            }

            if ($item['field'] == $field) {
                $item['order']++;
                return $item;
            }

            return $item;
        });

        $this->sortFields[$mode] = $this->_sortFieldsByOrder($map);
    }

    private function _generateFiles()
    {
        $code = $this->_generateComponentCode();
        $html = $this->_generateViewHtml();
        $props = [
            'modelPath' => $this->modelPath,
            'model' => $this->_getModelName(),
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
            'props' => $props,
            '--child' => false,
        ]);

        if ($this->exitCode == 0) {
            if ($this->_isAddFeatureEnabled() || $this->_isEditFeatureEnabled() || $this->_isDeleteFeatureEnabled()) {
                $this->exitCode = Artisan::call('livewire:tall-crud-generator', [
                    'name' => $this->componentName . 'Child',
                    'props' => $props,
                    '--child' => true,
                ]);
            }
        }

        $this->generatedCode = "@livewire('" . $this->componentName . "')";
        $this->isComplete = true;
    }
}
