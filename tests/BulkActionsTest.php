<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class BulkActionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
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
        
        $this->assertTrue($tallProperties->isBulkActionsEnabled());
        $this->assertEquals('status', $tallProperties->getBulkActionColumn());
        $this->assertEquals('Product', $tallProperties->getModelName());
        $this->assertEquals('id', $tallProperties->getPrimaryKey());

        $bulkActionCode = $componentCode->getBulkActionsCode();
        $this->assertNotEmpty($bulkActionCode['vars']);
        $this->assertNotEmpty($bulkActionCode['method']);
    }
}
