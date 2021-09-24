<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class EagerLoadTest extends TestCase
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

    public function test_eager_load_is_visible()
    {
        $this->component
            ->assertSee('Eager Load')
            ->assertSee('Eager Load a Relationship');
    }

    public function test_relations_is_wired()
    {
        $this->component
            ->call('createNewWithRelation')
            ->assertPropertyWired('withRelation.name')
            ->assertSeeHtml('<option value="categories">categories</option>')
            ->assertSeeHtml('<option value="tags">tags</option>')
            ->assertSeeHtml('<option value="tracks">tracks</option>')
            ->assertSeeHtml('<option value="comments">comments</option>')
            ->assertSeeHtml('<option value="brand">brand</option>');
    }

    public function test_relation_is_required()
    {
        $this->component
            ->call('createNewWithRelation')
            ->call('addWithRelation')
            ->assertHasErrors('withRelation.name')
            ->assertSee('Please select a Relation');
    }

    public function test_selecting_relation_shows_other_fields()
    {
        $this->component
            ->call('createNewWithRelation')
            ->set('withRelation.name', 'brand')
            ->assertPropertyWired('withRelation.displayColumn')
            ->assertCount('withRelation.columns', 4);
    }

    public function test_column_is_required()
    {
        $this->component
            ->call('createNewWithRelation')
            ->set('withRelation.name', 'brand')
            ->call('addWithRelation')
            ->assertHasErrors('withRelation.displayColumn')
            ->assertSee('Please select a value.');
    }

    public function test_eager_load_can_be_added()
    {
        $this->component
            ->call('createNewWithRelation')
            ->set('withRelation.name', 'brand')
            ->set('withRelation.displayColumn', 'name')
            ->call('addWithRelation')
            ->assertViewHas('withRelations')
            ->assertCount('withRelations', 1)
            ->assertMethodWired('deleteWithRelation')
            ->assertSeeInOrder(['brand', 'name']);
    }

    public function test_duplicate_can_not_be_added()
    {
        $this->component
            ->setStandardEagerLoadRelations()
            ->call('createNewWithRelation')
            ->set('withRelation.name', 'brand')
            ->assertHasErrors('withRelation.name')
            ->assertSee('Relation Already Defined.');
    }

    public function test_eager_load_can_be_deleted()
    {
        $this->component
            ->setStandardEagerLoadRelations()
            ->call('deleteWithRelation', 1)
            ->assertCount('withRelations', 2)
            ->assertSeeInOrder(['brand', 'tags']);
    }

    public function test_query_eager_loads_the_relations()
    {
        $this->component
            ->setStandardEagerLoadRelations()
            ->pressNext(3)
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');
        $props = $this->component->get('props');

        $this->assertEquals(['brand', 'categories', 'tags'], $tallProperties->getEagerLoadModels()->toArray());
        $this->assertStringContainsString("->with(['brand','categories','tags'])", $componentCode->getWithQueryCode());
        $this->assertStringContainsString("->with(['brand','categories','tags'])", $props['code']['with_query']);
    }

    public function test_view_contain_eager_loaded_columns()
    {

        $this->component
            ->setStandardEagerLoadRelations()
            ->pressNext(3)
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $props = $this->component->get('props');

        $columns = $tallProperties->getListingColumns();
        $labels = $columns->map(function ($c) {
            return $c['label'];
        })->toArray();
        $this->assertEquals(
            ['Id', 'Name', 'Price', 'Sku', 'Brand', 'Categories', 'Tags'],
            $labels
        );

        $this->assertStringContainsString("Brand</td>", $props['html']['table_header']);
        $this->assertStringContainsString("Categories</td>", $props['html']['table_header']);
        $this->assertStringContainsString("Tags</td>", $props['html']['table_header']);
    }

    public function test_slot_contain_eager_loaded_columns()
    {

        $this->component
            ->setStandardEagerLoadRelations()
            ->pressNext(3)
            ->generateFiles();

        $props = $this->component->get('props');

        $brandSlotCode = <<<'EOT'
{{ $result->brand?->name}}</td>
EOT;
        $categorySlotCode = <<<'EOT'
{{ $result->categories->implode('name', ',')}}</td>
EOT;
        $tagSlotCode = <<<'EOT'
{{ $result->tags->implode('name', ',')}}</td>
EOT;

        $this->assertStringContainsString($brandSlotCode, $props['html']['table_slot']);
        $this->assertStringContainsString($categorySlotCode, $props['html']['table_slot']);
        $this->assertStringContainsString($tagSlotCode, $props['html']['table_slot']);
    }
}
