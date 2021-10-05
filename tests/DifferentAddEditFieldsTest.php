<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\TallProperties;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

class DifferentAddEditFieldsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->pressNext();
    }

    public function test_without_relations()
    {
        $this->component
            ->setStandardFields()
            ->set('fields.2.inEdit', false)
            ->set('fields.3.inAdd', false)
            ->pressNext(4)
            ->generateFiles();

        $tallProperties = App::make(TallProperties::class);

        $addColumns = $tallProperties->getAddFormFields()->map(function ($f) {
            return $f['column'];
        })->toArray();
        $this->assertEquals(['name', 'price', 'sku'], $addColumns);

        $editColumns = $tallProperties->getEditFormFields()->map(function ($f) {
            return $f['column'];
        })->toArray();
        $this->assertEquals(['name', 'price', 'status'], $editColumns);
    }

    public function test_with_btm_combo_and_no_belongs_to()
    {
        $this->component
            ->setStandardFields()
            ->set('fields.2.inEdit', false)
            ->set('fields.3.inAdd', false)
            ->pressNext()

            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->set('belongsToManyRelation.inEdit', false)
            ->set('belongsToManyRelation.displayColumn', 'name')
            ->call('addBelongsToManyRelation')
            ->call('createNewBelongsToManyRelation')

            ->set('belongsToManyRelation.name', 'categories')
            ->set('belongsToManyRelation.inAdd', false)
            ->set('belongsToManyRelation.displayColumn', 'name')
            ->set('belongsToManyRelation.isMultiSelect', true)
            ->call('addBelongsToManyRelation')
            ->pressNext(3)
            ->generateFiles();

        $childComponentCode = App::make(ChildComponentCode::class);

        $btmInitCode = $childComponentCode->getBtmInitCode();

        $tagInitCode = <<<'EOT'
$this->tags = Tag::orderBy('name')->get();
EOT;

        $categoryInitCode = <<<'EOT'
$this->categories = Category::orderBy('name')->get();
EOT;

        $this->assertStringContainsString($tagInitCode, $btmInitCode);
        $this->assertStringNotContainsString($categoryInitCode, $btmInitCode);

        $btmAttachCode = $childComponentCode->getBtmAttachCode();
        $tagAttachCode = '$item->tags()->attach($this->checkedTags);';
        $categoryAttachCode = '$item->categories()->attach($this->checkedCategories);';
    
        $this->assertStringContainsString($tagAttachCode, $btmAttachCode);
        $this->assertStringNotContainsString($categoryAttachCode, $btmAttachCode);

        $btmFetchCode = $childComponentCode->getBtmFetchCode();
        $fetchTagCode = <<<'EOT'
    $this->tags = Tag::orderBy('name')->get();
    EOT;
        $fetchCategoryCode = <<<'EOT'
    $this->categories = Category::orderBy('name')->get();
    EOT;
    
        $this->assertStringNotContainsString($fetchTagCode, $btmFetchCode);
        $this->assertStringContainsString($fetchCategoryCode, $btmFetchCode);

        $btmUpdateCode = $childComponentCode->getBtmUpdateCode();

        $tagSyncCode = '$this->item->tags()->sync($this->checkedTags);';
        $categorySyncCode = '$this->item->categories()->sync($this->checkedCategories);';
    
        $this->assertStringNotContainsString($tagSyncCode, $btmUpdateCode);
        $this->assertStringContainsString($categorySyncCode, $btmUpdateCode);
    }

    public function test_belongs_to_in_add_only()
    {
        $this->component
            ->setStandardFields()
            ->pressNext()

            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->set('belongsToRelation.displayColumn', 'name')
            ->set('belongsToRelation.inEdit', false)
            ->call('addBelongsToRelation')
            ->pressNext(3)
            ->generateFiles();

        $childComponentCode = App::make(ChildComponentCode::class);

        $code = $childComponentCode->getBelongsToInitCode();
        $belongsToInitCode = <<<'EOT'
$this->brands = Brand::orderBy('name')->get();
EOT;
        $this->assertStringContainsString($belongsToInitCode, $code);

        $code = $childComponentCode->getBelongsToInitCode(false);
        $this->assertEmpty($code);
    }

    public function test_belongs_to_in_edit_only()
    {
        $this->component
            ->setStandardFields()
            ->pressNext()

            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->set('belongsToRelation.displayColumn', 'name')
            ->set('belongsToRelation.inAdd', false)
            ->call('addBelongsToRelation')
            ->pressNext(3)
            ->generateFiles();

        $childComponentCode = App::make(ChildComponentCode::class);

        $code = $childComponentCode->getBelongsToInitCode(false);
        $belongsToInitCode = <<<'EOT'
$this->brands = Brand::orderBy('name')->get();
EOT;
        $this->assertStringContainsString($belongsToInitCode, $code);

        $code = $childComponentCode->getBelongsToInitCode();
        $this->assertEmpty($code);
    }
}
