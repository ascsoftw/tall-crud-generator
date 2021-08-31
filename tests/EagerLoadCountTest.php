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
    }

    public function test_adding_a_eager_load_count_relation()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()

            ->call('createNewWithCountRelation')
            ->assertPropertyWired('withCountRelation.name')
            ->call('addWithCountRelation')
            ->assertHasErrors('withCountRelation.name')
            ->assertSee('Please select a Relation')
            ->assertSeeHtml('<option value="categories">categories</option>')
            ->assertSeeHtml('<option value="tags">tags</option>')
            ->assertSeeHtml('<option value="tracks">tracks</option>')
            ->assertSeeHtml('<option value="comments">comments</option>')
            ->assertSeeHtml('<option value="brand">brand</option>')

            ->set('withCountRelation.name', 'tags')
            ->assertPropertyWired('withCountRelation.isSortable')
            ->call('addWithCountRelation')

            ->assertViewHas('withCountRelations')
            ->assertCount('withCountRelations', 1)
            ->assertMethodWired('deleteWithCountRelation')

            ->call('createNewWithCountRelation')
            ->set('withCountRelation.name', 'tags')
            ->assertHasErrors('withCountRelation.name')
            ->assertSee('Relation Already Defined.')

            ->set('withCountRelation.name', 'categories')
            ->set('withCountRelation.isSortable', 11)
            ->call('addWithCountRelation')

            ->assertCount('withCountRelations', 2)
            ->assertSeeInOrder(['tags', 'No',  'categories', 'Yes'])

            ->call('deleteWithCountRelation', 0)
            ->assertCount('withCountRelations', 1)
            ->assertSeeInOrder(['categories', 'Yes'])

            ->pressNext(3)
            ->generateFiles()
            ->assertCount('withCountRelations', 1);

        $props = $this->component->get('props');
        $this->assertNotEmpty($props['code']['with_count_query']);
    }
}
