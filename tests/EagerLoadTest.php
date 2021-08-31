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
    }

    public function test_eager_load_is_visible()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()
            ->assertSee('Eager Load')
            ->assertSee('Eager Load a Relationship')
            ->assertDontSee('Belongs To');
    }

    public function test_adding_a_eager_load_relation()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()

            ->call('createNewWithRelation')
            ->assertPropertyWired('withRelation.name')
            ->call('addWithRelation')
            ->assertHasErrors('withRelation.name')
            ->assertSee('Please select a Relation')
            ->assertSeeHtml('<option value="categories">categories</option>')
            ->assertSeeHtml('<option value="tags">tags</option>')
            ->assertSeeHtml('<option value="tracks">tracks</option>')
            ->assertSeeHtml('<option value="comments">comments</option>')
            ->assertSeeHtml('<option value="brand">brand</option>')

            ->set('withRelation.name', 'brand')
            ->assertPropertyWired('withRelation.displayColumn')
            ->assertCount('withRelation.columns', 4)
            ->call('addWithRelation')
            ->assertHasErrors('withRelation.displayColumn')
            ->assertSee('Please select a value.')

            ->set('withRelation.displayColumn', 'name')
            ->call('addWithRelation')
            ->assertViewHas('withRelations')
            ->assertCount('withRelations', 1)
            ->assertMethodWired('deleteWithRelation')

            ->call('createNewWithRelation')
            ->set('withRelation.name', 'brand')
            ->assertHasErrors('withRelation.name')
            ->assertSee('Relation Already Defined.')

            ->set('withRelation.name', 'tags')
            ->set('withRelation.displayColumn', 'updated_at')
            ->call('addWithRelation')

            ->assertCount('withRelations', 2)
            ->assertSeeInOrder(['brand', 'name',  'tags', 'updated_at'])

            ->call('deleteWithRelation', 1)
            ->assertCount('withRelations', 1)
            ->assertSeeInOrder(['brand', 'name'])

            ->pressNext(3)
            ->generateFiles()
            ->assertCount('withRelations', 1);

        $props = $this->component->get('props');
        $this->assertNotEmpty($props['code']['with_query']);
    }
}
