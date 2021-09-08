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
    }

    public function test_belongs_to_is_visible()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()
            ->assertSeeInOrder(['Belongs To Many', 'Belongs To']);
    }

    public function test_adding_a_belongs_to_relation()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()

            ->call('createNewBelongsToRelation')
            ->assertPropertyWired('belongsToRelation.name')
            ->call('addBelongsToRelation')
            ->assertHasErrors('belongsToRelation.name')
            ->assertSee('Please select a Relation')
            ->assertCount('allRelations.belongsTo', 1)

            ->set('belongsToRelation.name', 'brand')
            ->assertPropertyWired('belongsToRelation.inAdd')
            ->assertPropertyWired('belongsToRelation.inEdit')
            ->assertPropertyWired('belongsToRelation.displayColumn')
            ->assertSet('belongsToRelation.inAdd', true)
            ->assertSet('belongsToRelation.inEdit', true)
            ->assertCount('belongsToRelation.columns', 4)

            ->call('addBelongsToRelation')
            ->assertHasErrors('belongsToRelation.displayColumn')
            ->assertSee('Please select a value.')

            ->set('belongsToRelation.displayColumn', 'name')
            ->call('addBelongsToRelation')
            ->assertViewHas('belongsToRelations')
            ->assertCount('belongsToRelations', 1)
            ->assertMethodWired('deleteBelongsToRelation')

            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->assertHasErrors('belongsToRelation.name')
            ->assertSee('Relation Already Defined.')
            ->set('confirmingBelongsTo', false)

            ->assertCount('belongsToRelations', 1)
            ->assertSeeInOrder(['brand', 'name'])

            ->call('deleteBelongsToRelation', 0)
            ->assertCount('belongsToRelations', 0)

            ->call('createNewBelongsToRelation')
            ->set('belongsToRelation.name', 'brand')
            ->set('belongsToRelation.displayColumn', 'name')
            ->call('addBelongsToRelation')
            ->assertCount('belongsToRelations', 1)

            ->pressNext(3)
            ->generateFiles()
            ->assertCount('belongsToRelations', 1);

        // $this->component->call('isBelongsToAddEnabled')
        //     ->assertReturnEquals('isBelongsToAddEnabled', true);

        // $this->component->call('isBelongsToEditEnabled')
        //     ->assertReturnEquals('isBelongsToEditEnabled', true);

        // $this->component->call('isBelongsToEnabled')
        //     ->assertReturnEquals('isBelongsToEnabled', true);
    }
}
