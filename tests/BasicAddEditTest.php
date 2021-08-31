<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class BasicAddEditTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext();
    }

    public function test_default_settings()
    {
        $this->component
            ->assertSet('step', 3)
            ->assertSee('Display In Listing')
            ->assertSee('Display In Create')
            ->assertSee('Display In Edit')
            ->assertMethodWired('moveBack')
            ->assertMethodWired('moveAhead');
    }

    public function test_at_least_1_add_field_is_required()
    {
        $this->component
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inAdd', false)
            ->pressNext()
            ->assertSee('Please select at least 1 Field to Display in Create Column.')
            ->assertSet('step', 3);
    }

    public function test_at_least_1_edit_field_is_required()
    {
        $this->component
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.inEdit', false)
            ->pressNext()
            ->assertSee('Please select at least 1 Field to Display in Edit Column.')
            ->assertSet('step', 3);
    }

    public function test_attributes_is_visible()
    {
        $this->component
            ->call('addField')
            ->assertMethodWired('showAttributes');
    }

    public function test_validation_can_be_applied()
    {
        $this->component
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->assertMethodWired('showAttributes')
            ->call('showAttributes', 0)
            ->assertPropertyWired('attributes.rules')
            ->assertPropertyWired('attributes.type')

            ->call('addRule', 'required')
            ->assertSet('attributes.rules', 'required,')

            ->call('setAttributes');

        $fields = $this->component->get('fields');
        $this->assertEquals('required,', $fields[0]['attributes']['rules']);
    }

    public function test_field_type_can_be_changed()
    {
        $this->component
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->call('showAttributes', 0)
            ->call('addRule', 'required')
            ->set('attributes.type', 'checkbox')
            ->call('setAttributes');

        $fields = $this->component->get('fields');
        $this->assertEquals('checkbox', $fields[0]['attributes']['type']);
    }

    public function test_our_standard_format()
    {
        $this->component
            ->setStandardFields()
            ->pressNext(4)
            ->assertSet('step', 7)
            ->generateFiles()
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);

        $props = $this->component->get('props');
        $this->assertNotEmpty($props['code']['sort']['vars']);
        $this->assertNotEmpty($props['code']['sort']['query']);
        $this->assertNotEmpty($props['code']['sort']['method']);
        $this->component->call('isSortingEnabled')
                ->assertReturnEquals('isSortingEnabled', true);
        $this->component->call('isPrimaryKeySortable')
                ->assertReturnEquals('isPrimaryKeySortable', true);
        $this->component->call('getDefaultSortableColumn')
                ->assertReturnEquals('getDefaultSortableColumn', 'id');

        $this->assertNotEmpty($props['code']['search']['vars']);
        $this->assertNotEmpty($props['code']['search']['query']);
        $this->assertNotEmpty($props['code']['search']['method']);
        $this->component->call('isSearchingEnabled')
                ->assertReturnEquals('isSearchingEnabled', true);
        $this->component->call('getSearchableColumns')
                ->assertReturnCount('getSearchableColumns', 1);

        $this->assertNotEmpty($props['code']['pagination_dropdown']['method']);
        $this->component->call('isPaginationDropdownEnabled')
                ->assertReturnEquals('isPaginationDropdownEnabled', true);

        $this->assertNotEmpty($props['code']['pagination']['vars']);
        $advancedSettings = $this->component->get('advancedSettings');
        $this->assertEquals(15, $advancedSettings['table_settings']['recordsPerPage']);

        $this->assertEmpty($props['code']['with_query']);
        $this->assertEmpty($props['code']['with_count_query']);

        $this->assertEmpty($props['code']['hide_columns']['vars']);
        $this->assertEmpty($props['code']['hide_columns']['init']);
        $this->component->call('isHideColumnsEnabled')
                ->assertReturnEquals('isHideColumnsEnabled', false);

        $this->assertEmpty($props['code']['bulk_actions']['vars']);
        $this->assertEmpty($props['code']['bulk_actions']['method']);
        $this->component->call('isBulkActionsEnabled')
                ->assertReturnEquals('isBulkActionsEnabled', false);

        $this->assertEmpty($props['code']['filter']['vars']);
        $this->assertEmpty($props['code']['filter']['init']);
        $this->assertEmpty($props['code']['filter']['query']);
        $this->assertEmpty($props['code']['filter']['method']);
        $this->component->call('isFilterEnabled')
                ->assertReturnEquals('isFilterEnabled', false);

        $this->assertEmpty($props['code']['other_models']);

        $this->assertNotEmpty($props['code']['child_delete']['vars']);
        $this->assertNotEmpty($props['code']['child_delete']['method']);
        $this->component->call('isDeleteFeatureEnabled')
                ->assertReturnEquals('isDeleteFeatureEnabled', true);

        $this->assertNotEmpty($props['code']['child_add']['vars']);
        $this->assertNotEmpty($props['code']['child_add']['method']);
        $this->component->call('isAddFeatureEnabled')
                ->assertReturnEquals('isAddFeatureEnabled', true);

        $this->assertNotEmpty($props['code']['child_edit']['vars']);
        $this->assertNotEmpty($props['code']['child_edit']['method']);
        $this->component->call('isEditFeatureEnabled')
                ->assertReturnEquals('isEditFeatureEnabled', true);

        $this->assertEmpty($props['code']['child_other_models']);
        $this->assertEmpty($props['code']['child_vars']);
        $this->component->call('isBtmEnabled')
                ->assertReturnEquals('isBtmEnabled', false);
        $this->component->call('isBelongsToEnabled')
                ->assertReturnEquals('isBelongsToEnabled', false);

        //Test View Code
        $this->assertNotEmpty($props['html']['add_link']);

        $this->assertNotEmpty($props['html']['search_box']);

        $this->assertNotEmpty($props['html']['pagination_dropdown']);

        $this->assertEmpty($props['html']['hide_columns']);

        $this->assertEmpty($props['html']['bulk_action']);

        $this->assertEmpty($props['html']['filter_dropdown']);

        $this->assertNotEmpty($props['html']['child_component']);

        $this->assertNotEmpty($props['html']['flash_component']);
        $this->component->call('isFlashMessageEnabled')
                ->assertReturnEquals('isFlashMessageEnabled', true);

        $this->assertNotEmpty($props['html']['child']['delete_modal']);
        $this->assertNotEmpty($props['html']['child']['add_modal']);
        $this->assertNotEmpty($props['html']['child']['edit_modal']);

        $this->assertEquals(5, substr_count($props['html']['table_header'], '</x:tall-crud-generator::table-column>'));
        $this->assertEquals(5, substr_count($props['html']['table_slot'], '</x:tall-crud-generator::table-column>'));
    }
}
