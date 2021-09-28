<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\Template;
use Livewire\Livewire;

class BulkActionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext(3);
    }

    public function test_bulk_actions_is_disabled_by_default()
    {
        $this->component
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');

        $this->assertFalse($tallProperties->isBulkActionsEnabled());
        $bulkActionCode = $componentCode->getBulkActionsCode();
        $this->assertEmpty($bulkActionCode['vars']);
        $this->assertEmpty($bulkActionCode['method']);
    }


    public function test_bulk_actions_is_disabled_if_no_column_selected()
    {
        $this->component
            ->set('advancedSettings.table_settings.bulkActions', true)
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');

        $this->assertFalse($tallProperties->isBulkActionsEnabled());
        $bulkActionCode = $componentCode->getBulkActionsCode();
        $this->assertEmpty($bulkActionCode['vars']);
        $this->assertEmpty($bulkActionCode['method']);
    }

    public function test_bulk_actions_can_be_enabled()
    {
        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.bulkActions')
            ->assertSet('advancedSettings.table_settings.bulkActions', false)
            ->assertDontSee('Column to Change on Bulk Action')
            ->set('advancedSettings.table_settings.bulkActions', true)
            ->assertSee('Column to Change on Bulk Action')
            ->assertPropertyWired('advancedSettings.table_settings.bulkActionColumn')
            ->assertCount('modelProps.columns', 7)
            ->set('advancedSettings.table_settings.bulkActionColumn', 'status')
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');
        $props = $this->component->get('props');
        
        $this->assertTrue($tallProperties->isBulkActionsEnabled());
        $this->assertEquals('status', $tallProperties->getBulkActionColumn());
        $this->assertEquals('Product', $tallProperties->getModelName());
        $this->assertEquals('id', $tallProperties->getPrimaryKey());

        $bulkActionCode = $componentCode->getBulkActionsCode();
        
        $this->assertStringContainsString('public $selectedItems = []', $bulkActionCode['vars']);
        $this->assertEquals(Template::getHideColumnInitTemplate(), $componentCode->getHideColumnInitCode());
        $this->assertStringContainsString('public $selectedItems = []', $props['code']['bulk_actions']['vars']);
    }

    public function test_view_contains_dropdown()
    {
        $this->component
            ->set('advancedSettings.table_settings.bulkActions', true)
            ->set('advancedSettings.table_settings.bulkActionColumn', 'status')
            ->pressNext()
            ->generateFiles();
        
        $props = $this->component->get('props');

        $this->assertEquals(Template::getBulkActionTemplate(), $props['html']['bulk_action']);
    }

    public function test_table_header_contains_bulk_column()
    {
        $this->component
            ->set('advancedSettings.table_settings.bulkActions', true)
            ->set('advancedSettings.table_settings.bulkActionColumn', 'status')
            ->pressNext()
            ->generateFiles();
        
        $props = $this->component->get('props');

        $this->assertStringContainsString('width="10"></td>', $props['html']['table_header']);
    }

    public function test_table_slot_contains_bulk_column()
    {
        $this->component
            ->set('advancedSettings.table_settings.bulkActions', true)
            ->set('advancedSettings.table_settings.bulkActionColumn', 'status')
            ->pressNext()
            ->generateFiles();
        
        $props = $this->component->get('props');
        $this->assertStringContainsString('wire:model.defer="selectedItems"', $props['html']['table_slot']);
    }
}
