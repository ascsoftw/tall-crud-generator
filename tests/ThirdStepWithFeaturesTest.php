<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class ThirdStepWithFeaturesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1();
    }

    public function test_default_settings()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->assertSee('Display In Listing')
            ->assertSee('Display In Create')
            ->assertSee('Display In Edit')
            ->assertMethodWired('addField')
            ->assertSee('Delete')
            ->assertSee('Attribute');
    }

    public function test_column_is_selected_in_at_least_one_view()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->set('fields.0.inAdd', false)
            ->set('fields.0.inEdit', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('name Column should be selected to display in at least 1 view.');
    }

    public function test_column_is_selected_in_at_least_one_view_with_add_disabled()
    {
        $this->component
            ->set('componentProps.createAddModal', false)
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->set('fields.0.inEdit', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('name Column should be selected to display in at least 1 view.');
    }

    public function test_column_is_selected_in_at_least_one_view_with_edit_disabled()
    {
        $this->component
            ->set('componentProps.createEditModal', false)
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->set('fields.0.inAdd', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('name Column should be selected to display in at least 1 view.');
    }

    public function test_validate_display_column()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select at least 1 Field to Display in Listing Column.');
    }

    public function test_validate_display_column_with_only_add_enabled()
    {
        $this->component
            ->disableModals()
            ->set('componentProps.createAddModal', true)
            ->pressNext()
            ->assertDontSee('Display In Edit')
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Delete')
            ->assertSee('Attribute')
            ->assertSee('Please select at least 1 Field to Display in Listing Column.');
    }

    public function test_validate_display_column_with_only_edit_enabled()
    {
        $this->component
            ->disableModals()
            ->set('componentProps.createEditModal', true)
            ->pressNext()
            ->assertDontSee('Display In Create')
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inList', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Delete')
            ->assertSee('Attribute')
            ->assertSee('Please select at least 1 Field to Display in Listing Column.');
    }

    public function test_validate_create_column()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inAdd', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select at least 1 Field to Display in Create Column.');
    }

    public function test_validate_create_column_with_only_add_enabled()
    {
        $this->component
            ->disableModals()
            ->set('componentProps.createAddModal', true)
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inAdd', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select at least 1 Field to Display in Create Column.');
    }

    public function test_validate_edit_column()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inEdit', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select at least 1 Field to Display in Edit Column.');
    }

    public function test_validate_edit_column_with_only_edit_enabled()
    {
        $this->component
            ->disableModals()
            ->set('componentProps.createEditModal', true)
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inEdit', false)
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select at least 1 Field to Display in Edit Column.');
    }

    public function test_popular_validations()
    {
        $this->component
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->assertSet('confirmingAttributes', false)
            ->call('showFieldAttributes', 0)
            ->assertSet('confirmingFieldAttributes', true)
            ->assertSet('fieldAttributes.rules', '')
            ->assertSet('attributeKey', 0)
            ->assertSet('fieldAttributes.type', 'input')
            ->call('addRule', 'required')
            ->assertSet('fieldAttributes.rules', 'required,')
            ->assertSet('fields.0.fieldAttributes.rules', '')
            ->call('setFieldAttributes')
            ->assertSet('confirmingFieldAttributes', false)
            ->assertSet('fieldAttributeKey', false)
            ->assertSet('fields.0.fieldAttributes.rules', 'required,');
    }

    public function test_clear_options()
    {
        $this->component
        ->pressNext()
        ->call('addField')
        ->set('fields.0.column', 'name')
        ->set('fields.0.fieldAttributes.rules', 'required')
        ->call('showFieldAttributes', 0)
        ->assertSet('fieldAttributes.rules', 'required')
        ->call('clearRules')
        ->assertSet('fieldAttributes.rules', '');
    }
}
