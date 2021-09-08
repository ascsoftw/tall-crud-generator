<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;

class HideColumnsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext()
            ->eagerLoadStandardRelations()
            ->eagerLoadCountStandardRelations()
            ->pressNext(2);
    }

    public function test_hide_columns_is_disabled_by_default()
    {
        $this->component
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');

        $this->assertFalse($tallProperties->getHideColumnsFlag());
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
        $componentCode = $this->component->get('componentCode');
        
        $this->assertTrue($tallProperties->getHideColumnsFlag());

        $code = $componentCode->getHideColumnsCode();
        $this->assertNotEmpty($code['vars']);
        $this->assertNotEmpty($code['init']);

        $this->assertEquals(
            ['Id', 'Name', 'Price', 'Sku', 'Brand', 'Categories', 'Tags', 'Tags Count', 'Categories Count'], 
            $tallProperties->getListingColumns()->toArray()
        );

        $columnStr = <<<'EOT'
public $columns = ['Id','Name','Price','Sku','Brand','Categories','Tags','Tags Count','Categories Count']
EOT;
        $this->assertStringContainsString($columnStr, $code['vars']);

        $this->assertEquals(WithTemplates::getHideColumnInitTemplate(), $componentCode->getHideColumnInitCode());
    }
}
