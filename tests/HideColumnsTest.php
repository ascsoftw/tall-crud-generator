<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\Template;

class HideColumnsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext()
            ->setStandardEagerLoadRelations()
            ->setStandardEagerLoadCountRelations()
            ->pressNext(2);
    }

    public function test_hide_columns_is_disabled_by_default()
    {
        $this->component
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');

        $this->assertFalse($tallProperties->isHideColumnsEnabled());
        $code = $componentCode->getHideColumnsCode();
        $this->assertEmpty($code['vars']);
        $this->assertEmpty($code['init']);
    }


    public function test_hide_columns_can_be_enabled()
    {
        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.showHideColumns')
            ->assertSet('advancedSettings.table_settings.showHideColumns', false)
            ->set('advancedSettings.table_settings.showHideColumns', true)
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $this->assertTrue($tallProperties->isHideColumnsEnabled());
    }

    public function test_code_is_added_to_component()
    {

        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.showHideColumns')
            ->assertSet('advancedSettings.table_settings.showHideColumns', false)
            ->set('advancedSettings.table_settings.showHideColumns', true)
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');
        $props = $this->component->get('props');

        $code = $componentCode->getHideColumnsCode();
        $this->assertNotEmpty($code['vars']);
        $this->assertNotEmpty($code['init']);

        $columns = $tallProperties->getListingColumns();
        $labels = $columns->map(function ($c) {
            return $c['label'];
        })->toArray();
        $this->assertEquals(
            ['Id', 'Name', 'Price', 'Sku', 'Brand', 'Categories', 'Tags', 'Tags Count', 'Categories Count'], 
            $labels
        );

        $columnStr = <<<'EOT'
public $columns = ['Id','Name','Price','Sku','Brand','Categories','Tags','Tags Count','Categories Count']
EOT;
        $this->assertStringContainsString($columnStr, $code['vars']);
        $this->assertStringContainsString('public $selectedColumns = []', $code['vars']);

        $this->assertStringContainsString($columnStr, $props['code']['hide_columns']['vars']);
        $this->assertStringContainsString('public $selectedColumns = []', $props['code']['hide_columns']['vars']);

        $this->assertEquals(Template::getHideColumnInitTemplate(), $props['code']['hide_columns']['init']);
        $this->assertEquals(Template::getHideColumnInitTemplate(), $componentCode->getHideColumnInitCode());

        $this->assertEquals(Template::getHideColumnMethodTemplate(), $props['code']['hide_columns']['method']);
        $this->assertEquals(Template::getHideColumnMethodTemplate(), $componentCode->getHideColumnMethod());
    }


    public function test_view_contains_dropdown()
    {
        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.showHideColumns')
            ->assertSet('advancedSettings.table_settings.showHideColumns', false)
            ->set('advancedSettings.table_settings.showHideColumns', true)
            ->pressNext()
            ->generateFiles();

        $props = $this->component->get('props');

        $this->assertEquals(Template::getHideColumnDropdownTemplate(), $props['html']['hide_columns']);
    }

    public function test_table_header_has_hide_columns_code()
    {
        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.showHideColumns')
            ->assertSet('advancedSettings.table_settings.showHideColumns', false)
            ->set('advancedSettings.table_settings.showHideColumns', true)
            ->pressNext()
            ->generateFiles();
        
        $props = $this->component->get('props');
        $this->assertEquals(9, substr_count($props['html']['table_header'], '@if($this->showColumn('));
    }

    public function test_table_slot_has_hide_columns_code()
    {
        $this->component
            ->assertPropertyWired('advancedSettings.table_settings.showHideColumns')
            ->assertSet('advancedSettings.table_settings.showHideColumns', false)
            ->set('advancedSettings.table_settings.showHideColumns', true)
            ->pressNext()
            ->generateFiles();
        
        $props = $this->component->get('props');
        $this->assertEquals(9, substr_count($props['html']['table_slot'], '@if($this->showColumn('));
    }
}
