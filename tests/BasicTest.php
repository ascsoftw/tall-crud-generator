<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class BasicTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->hidePrimaryKeyFromListing()
            ->pressNext()
            ->call('addAllFields')
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext();
    }

    public function test_default_settings()
    {
        $this->component
            ->assertSet('step', 7)
            ->assertSee('Previous')
            ->assertDontSee('Next')
            ->assertMethodWired('moveBack')
            ->assertPropertyWired('componentName');
    }

    public function test_component_name_is_required()
    {
        $this->component
            ->pressNext()
            ->assertSee('Please enter the name of your component')
            ->assertHasErrors(['componentName' => 'required']);
    }

    public function test_component_is_generated()
    {
        $this->component
            ->generateFiles()
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);
        $generatedCode = $this->component->get('generatedCode');
        $this->assertStringContainsString('@livewire', $generatedCode);
        $this->assertEquals("@livewire('products')", $generatedCode);
    }

    public function test_various_features()
    {
        $this->component->generateFiles();
        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $this->assertEmpty($props['code']['sort']['vars']);
        $this->assertEmpty($props['code']['sort']['query']);
        $this->assertEmpty($props['code']['sort']['method']);
        $this->assertFalse($tallProperties->getSortingFlag());

        $this->assertEmpty($props['code']['search']['vars']);
        $this->assertEmpty($props['code']['search']['query']);
        $this->assertEmpty($props['code']['search']['method']);
        $this->assertFalse($tallProperties->getSearchingFlag());

        $this->assertEmpty($props['code']['pagination_dropdown']['method']);
        $this->assertFalse($tallProperties->getPaginationDropdownFlag());
        $paginationCode = $componentCode->getPaginationDropdownCode();
        $this->assertEmpty($paginationCode['method']);

        $this->assertNotEmpty($props['code']['pagination']['vars']);
        $this->assertEquals(15, $tallProperties->getRecordsPerPage());
        $this->assertStringContainsString('public $per_page = 15;', $props['code']['pagination']['vars']);

        $this->assertEmpty($props['code']['with_query']);
        $this->assertEmpty($props['code']['with_count_query']);

        $this->assertEmpty($props['code']['hide_columns']['vars']);
        $this->assertEmpty($props['code']['hide_columns']['init']);
        $this->assertFalse($tallProperties->getHideColumnsFlag());

        $this->assertEmpty($props['code']['bulk_actions']['vars']);
        $this->assertEmpty($props['code']['bulk_actions']['method']);
        $this->assertFalse($tallProperties->getBulkActionFlag());

        $this->assertEmpty($props['code']['filter']['vars']);
        $this->assertEmpty($props['code']['filter']['init']);
        $this->assertEmpty($props['code']['filter']['query']);
        $this->assertEmpty($props['code']['filter']['method']);
        $this->assertFalse($tallProperties->getFilterFlag());

        $this->assertEmpty($props['code']['other_models']);

        $this->assertEmpty($props['code']['child_delete']['vars']);
        $this->assertEmpty($props['code']['child_delete']['method']);
        $this->assertFalse($tallProperties->getDeleteFeatureFlag());

        $this->assertEmpty($props['code']['child_add']['vars']);
        $this->assertEmpty($props['code']['child_add']['method']);
        $this->assertFalse($tallProperties->getAddFeatureFlag());

        $this->assertEmpty($props['code']['child_edit']['vars']);
        $this->assertEmpty($props['code']['child_edit']['method']);
        $this->assertFalse($tallProperties->getEditFeatureFlag());

        $this->assertEmpty($props['code']['child_other_models']);
        $this->assertEmpty($props['code']['child_vars']);

        //Test View Code
        $this->assertEmpty($props['html']['add_link']);

        $this->assertEmpty($props['html']['search_box']);

        $this->assertEmpty($props['html']['pagination_dropdown']);

        $this->assertEmpty($props['html']['hide_columns']);

        $this->assertEmpty($props['html']['bulk_action']);

        $this->assertEmpty($props['html']['filter_dropdown']);

        $this->assertEmpty($props['html']['child_component']);

        $this->assertEmpty($props['html']['flash_component']);
        $this->assertFalse($tallProperties->getFlashMessageFlag());

        $this->assertEmpty($props['html']['child']['delete_modal']);
        $this->assertEmpty($props['html']['child']['add_modal']);
        $this->assertEmpty($props['html']['child']['edit_modal']);

        $this->assertEquals(7, substr_count($props['html']['table_header'], '</td>'));
        $this->assertEquals(7, substr_count($props['html']['table_slot'], '</td>'));
    }
}
