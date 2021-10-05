<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class ThirdStepWithoutFeaturesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->disableModals()
            ->pressNext();
    }

    public function test_default_settings()
    {
        $this->component
            ->assertSet('step', 3)
            ->assertSee('Previous')
            ->assertSee('Next')
            ->assertMethodWired('moveAhead')
            ->assertMethodWired('moveBack')
            ->assertDontSee('Display In Listing')
            ->assertDontSee('Display In Create')
            ->assertDontSee('Display In Edit')
            ->assertMethodWired('addAllFields')
            ->assertMethodWired('addField')
            ->assertDontSee('Delete')
            ->assertCount('fields', 0);
    }

    public function test_add_all_fields()
    {
        $this->component
            ->call('addAllFields')
            ->assertDontSee('Add All Fields')
            ->assertSee('Add New Field')
            ->assertSee('Delete')
            ->assertCount('fields', 7);
    }

    public function test_add_new_field()
    {
        $this->component
            ->call('addField')
            ->assertDontSee('Add All Fields')
            ->assertSee('Add New Field')
            ->assertMethodWired('deleteField')
            ->assertSee('Delete')
            ->assertDontSee('Attributes')
            ->assertCount('fields', 1);
    }

    public function test_that_at_least_one_field_is_added()
    {
        $this->component
            ->pressNext()
            ->assertSet('step', 3)
            ->assertSee('At least 1 Field should be added.');
    }

    public function test_that_column_is_not_empty()
    {
        $this->component
            ->call('addField')
            ->pressNext()
            ->assertSet('step', 3)
            ->assertCount('fields', 1)
            ->assertSee('Please select column for all fields.');
    }

    public function test_that_delete_is_working()
    {
        $this->component
            ->call('addField')
            ->call('addField')
            ->assertCount('fields', 2)
            ->call('deleteField', 0)
            ->assertCount('fields', 1)
            ->call('deleteField', 0)
            ->assertCount('fields', 0)
            ->assertMethodWired('addAllFields');
    }

    public function test_that_column_dropdown_is_populated()
    {
        $this->component
            ->call('addField')
            ->assertCount('modelProps.columns', 7);
    }

    public function test_that_there_are_no_duplicate_columns()
    {
        $this->component
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->call('addField')
            ->set('fields.1.column', 'name')
            ->assertCount('fields', 2)
            ->pressNext()
            ->assertSee('Please do not select a column more than once.');
    }

    public function test_we_can_move_back()
    {
        $this->component
            ->call('moveBack')
            ->assertSet('step', 2);
    }
}
