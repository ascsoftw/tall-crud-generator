<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class BelongsToTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext();
    }

    public function test_belongs_to_is_visible()
    {
        $this->component
            ->assertSeeInOrder(['Belongs To Many', 'Belongs To']);
    }

    public function test_relations_is_wired()
    {
        $this->component
            ->call('createNewBelongsToRelation')
            ->assertPropertyWired('belongsToRelation.name')
            ->assertCount('allRelations.belongsTo', 1);
    }

    public function test_relation_is_required()
    {
        $this->component
            ->call('createNewBelongsToRelation')
            ->call('addBelongsToRelation')
            ->assertHasErrors('belongsToRelation.name')
            ->assertSee('Please select a Relation');
    }

    public function test_selecting_relation_shows_other_fields()
    {
        $this->component
            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->assertPropertyWired('belongsToRelation.inAdd')
            ->assertPropertyWired('belongsToRelation.inEdit')
            ->assertPropertyWired('belongsToRelation.displayColumn')
            ->assertSet('belongsToRelation.inAdd', true)
            ->assertSet('belongsToRelation.inEdit', true)
            ->assertCount('belongsToRelation.columns', 4);
    }

    public function test_column_is_required()
    {
        $this->component
            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->call('addBelongsToRelation')
            ->assertHasErrors('belongsToRelation.displayColumn')
            ->assertSee('Please select a value.');
    }

    public function test_relation_can_be_added()
    {
        $this->component
            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->set('belongsToRelation.displayColumn', 'name')
            ->call('addBelongsToRelation')
            ->assertViewHas('belongsToRelations')
            ->assertCount('belongsToRelations', 1)
            ->assertMethodWired('deleteBelongsToRelation')
            ->assertSeeInOrder(['brand', 'name', 'Yes', 'Yes']);
    }

    public function test_duplicate_relation_can_not_be_added()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->assertHasErrors('belongsToRelation.name')
            ->assertSee('Relation Already Defined.');
    }

    public function test_relation_can_be_deleted()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->call('deleteBelongsToRelation', 0)
            ->assertCount('belongsToRelations', 0);
    }

    public function test_other_models_code_is_added()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $tallProperties = $this->component->get('tallProperties');
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $otherModels = $tallProperties->getBelongsToRelations()->toArray();
        $this->assertCount(1, $otherModels);
        $this->assertEquals("\nuse Ascsoftw\TallCrudGenerator\Tests\Models\Brand;", $childComponentCode->getChildOtherModelsCode());
        $this->assertEquals("\nuse Ascsoftw\TallCrudGenerator\Tests\Models\Brand;", $props['code']['child_other_models']);
    }

    public function test_rules_are_added()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $this->assertStringContainsString("'item.brand_id' => 'required',", $childComponentCode->getRulesForBelongsToFields());
        $this->assertStringContainsString("'item.brand_id' => 'required',", $props['code']['child_rules']);
    }

    public function test_attribute_is_added()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $this->assertStringContainsString("'item.brand_id' => 'Brand',", $childComponentCode->getAttributesForBelongsToFields());
        $this->assertStringContainsString("'item.brand_id' => 'Brand',", $props['code']['child_validation_attributes']);
    }

    public function test_vars_are_added()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $this->assertStringContainsString('public $brands = [];', $childComponentCode->getBelongsToVars());
        $this->assertStringContainsString('public $brands = [];', $props['code']['child_vars']);
    }

    public function test_add_init_code()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $belongsToInitCode = <<<'EOT'
$this->brands = Brand::orderBy('name')->get();
EOT;

        $this->assertStringContainsString($belongsToInitCode, $childComponentCode->getBelongsToInitCode());
        $this->assertStringContainsString($belongsToInitCode, $props['code']['child_add']['method']);
        
    }

    public function test_edit_init_code()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $belongsToInitCode = <<<'EOT'
$this->brands = Brand::orderBy('name')->get();
EOT;

        $this->assertStringContainsString($belongsToInitCode, $childComponentCode->getBelongsToInitCode(false));
        $this->assertStringContainsString($belongsToInitCode, $props['code']['child_edit']['method']);
        
    }

    public function test_save_code()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $belongsToInitCode = <<<'EOT'
'brand_id' => $this->item['brand_id'] ?? 0,
EOT;

        $this->assertStringContainsString($belongsToInitCode, $childComponentCode->getBelongsToSaveCode());
        $this->assertStringContainsString($belongsToInitCode, $props['code']['child_add']['method']);
        
    }

    public function test_add_modal()
    {

        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childViewCode = $this->component->get('childViewCode');
        $addModalCode = $childViewCode->getAddModal();

        $this->assertStringContainsString('wire:model.defer="item.brand_id"', $addModalCode);
        $this->assertStringContainsString("@error('item.brand_id')", $addModalCode);
        $this->assertStringContainsString('@foreach($brands as $c)', $addModalCode);

    }

    public function test_edit_modal()
    {
        $this->component
            ->setStandardBelongsToRelation()
            ->pressNext(3)
            ->generateFiles();
        
        $childViewCode = $this->component->get('childViewCode');
        $editModalCode = $childViewCode->getEditModal();

        $this->assertStringContainsString('wire:model.defer="item.brand_id"', $editModalCode);
        $this->assertStringContainsString("@error('item.brand_id')", $editModalCode);
        $this->assertStringContainsString('@foreach($brands as $c)', $editModalCode);
    }
}
