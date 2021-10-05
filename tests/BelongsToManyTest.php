<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class BelongsToManyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext();
    }

    public function test_belongs_to_many_is_visible()
    {
        $this->component
            ->assertSeeInOrder(['Belongs To Many', 'Belongs To']);
    }

    public function test_relations_is_wired()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->assertPropertyWired('belongsToManyRelation.name')
            ->assertCount('allRelations.belongsToMany', 3);
    }

    public function test_relation_is_required()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->call('addBelongsToManyRelation')
            ->assertHasErrors('belongsToManyRelation.name')
            ->assertSee('Please select a Relation');
    }

    public function test_selecting_relation_shows_other_fields()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->assertPropertyWired('belongsToManyRelation.inAdd')
            ->assertPropertyWired('belongsToManyRelation.inEdit')
            ->assertPropertyWired('belongsToManyRelation.isMultiSelect')
            ->assertPropertyWired('belongsToManyRelation.displayColumn')
            ->assertSet('belongsToManyRelation.inAdd', true)
            ->assertSet('belongsToManyRelation.inEdit', true)
            ->assertSet('belongsToManyRelation.isMultiSelect', false)
            ->assertCount('belongsToManyRelation.columns', 4);
    }

    public function test_column_is_required()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->call('addBelongsToManyRelation')
            ->assertHasErrors('belongsToManyRelation.displayColumn')
            ->assertSee('Please select a value.');
    }

    public function test_relation_can_be_added()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->set('belongsToManyRelation.displayColumn', 'name')
            ->call('addBelongsToManyRelation')
            ->assertViewHas('belongsToManyRelations')
            ->assertCount('belongsToManyRelations', 1)
            ->assertMethodWired('deleteBelongsToManyRelation')
            ->assertSeeInOrder(['tags', 'name', 'Yes', 'Yes']);
    }

    public function test_duplicate_relation_can_not_be_added()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->set('belongsToManyRelation.displayColumn', 'name')
            ->call('addBelongsToManyRelation')

            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->assertHasErrors('belongsToManyRelation.name')
            ->assertSee('Relation Already Defined.');
    }

    public function test_relation_can_be_deleted()
    {
        $this->component
            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->set('belongsToManyRelation.displayColumn', 'name')
            ->call('addBelongsToManyRelation')
            ->call('deleteBelongsToManyRelation', 0)
            ->assertCount('belongsToManyRelations', 0);
    }

    public function test_other_models_code_is_added()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $tallProperties = $this->component->get('tallProperties');
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $otherModels = $tallProperties->getBtmRelations()->toArray();
        $this->assertCount(2, $otherModels);

        $strToCheck = "\nuse Ascsoftw\TallCrudGenerator\Tests\Models\Tag;\nuse Ascsoftw\TallCrudGenerator\Tests\Models\Category;";
        $this->assertEquals($strToCheck, $childComponentCode->getOtherModelsCode());
        $this->assertEquals($strToCheck, $props['code']['child_other_models']);
    }

    public function test_vars_are_added()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $btmVars = $childComponentCode->getBtmVars();

        $this->assertStringContainsString('public $tags = [];', $btmVars);
        $this->assertStringContainsString('public $checkedTags = [];', $btmVars);
        $this->assertStringContainsString('public $categories = [];', $btmVars);
        $this->assertStringContainsString('public $checkedCategories = [];', $btmVars);

        $this->assertStringContainsString('public $tags = [];', $props['code']['child_vars']);
        $this->assertStringContainsString('public $checkedTags = [];', $props['code']['child_vars']);
        $this->assertStringContainsString('public $categories = [];', $props['code']['child_vars']);
        $this->assertStringContainsString('public $checkedCategories = [];', $props['code']['child_vars']);
    }

    public function test_init_code()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $btmInitCode = $childComponentCode->getBtmInitCode();

        $tagInitCode = <<<'EOT'
$this->tags = Tag::orderBy('name')->get();
EOT;
        $categoryInitCode = <<<'EOT'
$this->categories = Category::orderBy('name')->get();
EOT;

        $this->assertStringContainsString($tagInitCode, $btmInitCode);
        $this->assertStringContainsString('$this->checkedTags = [];', $btmInitCode);
        $this->assertStringContainsString($categoryInitCode, $btmInitCode);
        $this->assertStringContainsString('$this->checkedCategories = [];', $btmInitCode);

        $this->assertStringContainsString($tagInitCode, $props['code']['child_add']['method']);
        $this->assertStringContainsString('$this->checkedTags = [];', $props['code']['child_add']['method']);
        $this->assertStringContainsString($categoryInitCode, $props['code']['child_add']['method']);
        $this->assertStringContainsString('$this->checkedCategories = [];', $props['code']['child_add']['method']);

    }

    public function test_attach_code()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $btmAttachCode = $childComponentCode->getBtmAttachCode();
        $tagAttachCode = '$item->tags()->attach($this->checkedTags);';
        $categoryAttachCode = '$item->categories()->attach($this->checkedCategories);';

        $this->assertStringContainsString($tagAttachCode, $btmAttachCode);
        $this->assertStringContainsString($categoryAttachCode, $btmAttachCode);

        $this->assertStringContainsString($tagAttachCode, $props['code']['child_add']['method']);
        $this->assertStringContainsString($categoryAttachCode, $props['code']['child_add']['method']);

    }

    public function test_fetch_code()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $btmFetchCode = $childComponentCode->getBtmFetchCode();
        $checkedTagsCode = '$this->checkedTags = $product->tags->pluck("id")->map(function ($i) {';
        $checkedCategoriesCode = '$this->checkedCategories = $product->categories->pluck("id")->map(function ($i) {';
        $fetchTagCode = <<<'EOT'
$this->tags = Tag::orderBy('name')->get();
EOT;
        $fetchCategoryCode = <<<'EOT'
$this->categories = Category::orderBy('name')->get();
EOT;

        $this->assertStringContainsString($checkedTagsCode, $btmFetchCode);
        $this->assertStringContainsString($fetchTagCode, $btmFetchCode);

        $this->assertStringContainsString($checkedCategoriesCode, $btmFetchCode);
        $this->assertStringContainsString($fetchCategoryCode, $btmFetchCode);

        $this->assertStringContainsString($checkedTagsCode, $props['code']['child_edit']['method']);
        $this->assertStringContainsString($fetchTagCode, $props['code']['child_edit']['method']);
        $this->assertStringContainsString($checkedCategoriesCode, $props['code']['child_edit']['method']);
        $this->assertStringContainsString($fetchCategoryCode, $props['code']['child_edit']['method']);

    }

    public function test_update_code()
    {
        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childComponentCode = $this->component->get('childComponentCode');
        $props = $this->component->get('props');

        $btmUpdateCode = $childComponentCode->getBtmUpdateCode();

        $tagSyncCode = '$this->item->tags()->sync($this->checkedTags);';
        $categorySyncCode = '$this->item->categories()->sync($this->checkedCategories);';

        $this->assertStringContainsString($tagSyncCode, $btmUpdateCode);
        $this->assertStringContainsString($categorySyncCode, $btmUpdateCode);

        $this->assertStringContainsString('$this->checkedTags = [];', $btmUpdateCode);
        $this->assertStringContainsString('$this->checkedCategories = [];', $btmUpdateCode);

        $this->assertStringContainsString($tagSyncCode, $props['code']['child_edit']['method']);
        $this->assertStringContainsString($categorySyncCode, $props['code']['child_edit']['method']);
        $this->assertStringContainsString('$this->checkedTags = [];', $props['code']['child_edit']['method']);
        $this->assertStringContainsString('$this->checkedCategories = [];', $props['code']['child_edit']['method']);

    }

    public function test_add_modal()
    {

        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childViewCode = $this->component->get('childViewCode');
        $addModalCode = $childViewCode->getAddModal();

        $this->assertStringContainsString('wire:model.defer="checkedTags"', $addModalCode);
        $this->assertStringContainsString('{{$c->name}}</x:tall-crud-generator::label>', $addModalCode);
        $this->assertStringContainsString('@foreach( $tags as $c)', $addModalCode);
        $this->assertStringContainsString('value="{{ $c->id }}" ', $addModalCode);

        $this->assertStringContainsString('multiple="multiple" wire:model.defer="checkedCategories"', $addModalCode);
        $this->assertStringContainsString('<option value="{{ $c->id }}">{{$c->name}}</option>', $addModalCode);
        $this->assertStringContainsString('@foreach( $categories as $c)', $addModalCode);

    }

    public function test_edit_modal()
    {

        $this->component
            ->setStandardBtmRelations()
            ->pressNext(3)
            ->generateFiles();
        
        $childViewCode = $this->component->get('childViewCode');
        $editModalCode = $childViewCode->getEditModal();

        $this->assertStringContainsString('wire:model.defer="checkedTags"', $editModalCode);
        $this->assertStringContainsString('{{$c->name}}</x:tall-crud-generator::label>', $editModalCode);
        $this->assertStringContainsString('@foreach( $tags as $c)', $editModalCode);
        $this->assertStringContainsString('value="{{ $c->id }}" ', $editModalCode);

        $this->assertStringContainsString('multiple="multiple" wire:model.defer="checkedCategories"', $editModalCode);
        $this->assertStringContainsString('<option value="{{ $c->id }}">{{$c->name}}</option>', $editModalCode);
        $this->assertStringContainsString('@foreach( $categories as $c)', $editModalCode);

    }
}
