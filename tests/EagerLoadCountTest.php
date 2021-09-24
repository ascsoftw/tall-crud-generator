<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class EagerLoadCountTest extends TestCase
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

    public function test_relations_is_wired()
    {
        $this->component
            ->call('createNewWithCountRelation')
            ->assertPropertyWired('withCountRelation.name')
            ->assertSeeHtml('<option value="categories">categories</option>')
            ->assertSeeHtml('<option value="tags">tags</option>')
            ->assertSeeHtml('<option value="tracks">tracks</option>')
            ->assertSeeHtml('<option value="comments">comments</option>')
            ->assertSeeHtml('<option value="brand">brand</option>');
    }

    public function test_relation_is_required()
    {
        $this->component
            ->call('createNewWithCountRelation')
            ->call('addWithCountRelation')
            ->assertHasErrors('withCountRelation.name')
            ->assertSee('Please select a Relation');
    }

    public function test_selecting_relation_shows_other_fields()
    {
        $this->component
            ->call('createNewWithCountRelation')
            ->set('withCountRelation.name', 'tags')
            ->assertPropertyWired('withCountRelation.isSortable')
            ->assertSet('withCountRelation.isSortable', false);
    }

    public function test_eager_load_count_can_be_added()
    {
        $this->component
            ->call('createNewWithCountRelation')
            ->set('withCountRelation.name', 'tags')
            ->call('addWithCountRelation')
            ->assertViewHas('withCountRelations')
            ->assertCount('withCountRelations', 1)
            ->assertMethodWired('deleteWithCountRelation')
            ->assertSeeInOrder(['tags', 'No']);
    }

    public function test_duplicate_can_not_be_added()
    {
        $this->component
            ->setStandardEagerLoadCountRelations()
            ->call('createNewWithCountRelation')
            ->set('withCountRelation.name', 'tags')
            ->assertHasErrors('withCountRelation.name')
            ->assertSee('Relation Already Defined.');
    }

    public function test_eager_load_can_be_deleted()
    {
        $this->component
            ->setStandardEagerLoadCountRelations()
            ->call('deleteWithCountRelation', 0)
            ->assertCount('withCountRelations', 1)
            ->assertSeeInOrder(['categories', 'Yes']);
    }

    public function test_query_eager_loads_the_relations()
    {
        $this->component
            ->setStandardEagerLoadCountRelations()
            ->pressNext(3)
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $componentCode = $this->component->get('componentCode');
        $props = $this->component->get('props');

        $this->assertEquals(['tags', 'categories'], $tallProperties->getEagerLoadCountModels()->toArray());
        $this->assertStringContainsString("->withCount(['tags','categories'])", $componentCode->getWithCountQueryCode());
        $this->assertStringContainsString("->withCount(['tags','categories'])", $props['code']['with_count_query']);
    }

    public function test_view_contain_eager_loaded_count_columns()
    {

        $this->component
            ->setStandardEagerLoadCountRelations()
            ->pressNext(3)
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $props = $this->component->get('props');

        $columns = $tallProperties->getListingColumns();
        $labels = $columns->map(function ($c) {
            return $c['label'];
        })->toArray();
        $this->assertEquals(
            ['Id', 'Name', 'Price', 'Sku', 'Tags Count', 'Categories Count'],
            $labels
        );

        $this->assertStringContainsString("Tags Count</td>", $props['html']['table_header']);
        $this->assertStringContainsString("Categories Count</button>", $props['html']['table_header']);
    }

    public function test_slot_contain_eager_loaded_count_columns()
    {

        $this->component
            ->setStandardEagerLoadCountRelations()
            ->pressNext(3)
            ->generateFiles();

        $props = $this->component->get('props');

        $this->assertStringContainsString('{{ $result->tags_count}}</td>', $props['html']['table_slot']);
        $this->assertStringContainsString('{{ $result->categories_count}}</td>', $props['html']['table_slot']);
    }
}
