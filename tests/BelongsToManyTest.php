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
    }

    public function test_belongs_to_many_is_visible()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()
            ->assertSee('Belongs To Many');
    }

    public function test_adding_a_btm_relation()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext()

            ->call('createNewBelongsToManyRelation')
            ->assertPropertyWired('belongsToManyRelation.name')
            ->call('addBelongsToManyRelation')
            ->assertHasErrors('belongsToManyRelation.name')
            ->assertSee('Please select a Relation')
            ->assertCount('allRelations.belongsToMany', 3)

            ->set('belongsToManyRelation.name', 'tags')
            ->assertPropertyWired('belongsToManyRelation.inAdd')
            ->assertPropertyWired('belongsToManyRelation.inEdit')
            ->assertPropertyWired('belongsToManyRelation.isMultiSelect')
            ->assertPropertyWired('belongsToManyRelation.displayColumn')
            ->assertSet('belongsToManyRelation.inAdd', true)
            ->assertSet('belongsToManyRelation.inEdit', true)
            ->assertSet('belongsToManyRelation.isMultiSelect', false)
            ->assertCount('belongsToManyRelation.columns', 4)

            ->call('addBelongsToManyRelation')
            ->assertHasErrors('belongsToManyRelation.displayColumn')
            ->assertSee('Please select a value.')

            ->set('belongsToManyRelation.displayColumn', 'name')
            ->call('addBelongsToManyRelation')
            ->assertViewHas('belongsToManyRelations')
            ->assertCount('belongsToManyRelations', 1)
            ->assertMethodWired('deleteBelongsToManyRelation')

            ->call('createNewBelongsToManyRelation')
            ->set('belongsToManyRelation.name', 'tags')
            ->assertHasErrors('belongsToManyRelation.name')
            ->assertSee('Relation Already Defined.')

            ->set('belongsToManyRelation.name', 'categories')
            ->set('belongsToManyRelation.displayColumn', 'updated_at')
            ->call('addBelongsToManyRelation')

            ->assertCount('belongsToManyRelations', 2)
            ->assertSeeInOrder(['tags', 'name',  'categories', 'updated_at'])

            ->call('deleteBelongsToManyRelation', 1)
            ->assertCount('belongsToManyRelations', 1)
            ->assertSeeInOrder(['tags', 'name'])

            ->pressNext(3)
            ->generateFiles()
            ->assertCount('belongsToManyRelations', 1);

        // $this->component->call('isBtmAddEnabled')
        //     ->assertReturnEquals('isBtmAddEnabled', true);

        // $this->component->call('isBtmEditEnabled')
        //     ->assertReturnEquals('isBtmEditEnabled', true);

        $this->component->call('isBtmEnabled')
            ->assertReturnEquals('isBtmEnabled', true);
    }
}
