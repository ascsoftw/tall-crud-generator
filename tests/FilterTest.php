<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class FilterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext(3);
    }

    public function test_filter_can_be_added()
    {
        $this->component
            ->call('createNewFilter')
            ->assertPropertyWired('filter.type')
            ->call('addFilter')
            ->assertHasErrors('filter.type')
            ->assertSee('Please select a value.')

            ->set('filter.type', 'None')
            ->assertPropertyWired('filter.column')
            ->assertPropertyWired('filter.options')
            ->assertCount('filter.columns', 7)
            ->call('addFilter')
            ->assertHasErrors('filter.column')
            ->assertSee('Please select a value.')

            ->set('filter.column', 'name')
            ->call('addFilter')
            ->assertViewHas('filters')
            ->assertCount('filters', 1)
            ->assertMethodWired('deleteFilter')

            ->assertSeeInOrder(['None', 'name'])

            ->call('createNewFilter')
            ->set('filter.type', 'BelongsTo')
            ->assertPropertyWired('filter.relation')
            ->assertCount('allRelations.belongsTo', 1)
            ->call('addFilter')
            ->assertHasErrors('filter.relation')
            ->assertSee('Please select a value.')

            ->set('filter.relation', 'brand')
            ->assertPropertyWired('filter.column')
            ->assertCount('filter.columns', 4)
            ->call('addFilter')
            ->assertHasErrors('filter.column')
            ->assertSee('Please select a value.')

            ->set('filter.column', 'name')
            ->call('addFilter')
            ->assertViewHas('filters')
            ->assertCount('filters', 2)
            ->assertMethodWired('deleteFilter')

            ->assertSeeInOrder(['None', 'name', 'BelongsTo', 'brand.name'])
            ->call('deleteFilter', 1)
            ->assertCount('filters', 1)
            ->assertSeeInOrder(['None', 'name'])

            ->call('createNewFilter')
            ->set('filter.type', 'BelongsToMany')
            ->assertCount('allRelations.belongsToMany', 3)
            ->set('filter.relation', 'categories')
            ->set('filter.column', 'name')
            ->call('addFilter')

            ->assertViewHas('filters')
            ->assertCount('filters', 2)
            ->assertSeeInOrder(['None', 'name', 'BelongsToMany', 'categories.name'])

            ->call('createNewFilter')
            ->set('filter.type', 'BelongsTo')
            ->set('filter.relation', 'brand')
            ->set('filter.column', 'name')
            ->call('addFilter')

            ->pressNext()
            ->generateFiles();
        
        $tallComponent = $this->component->get('tallComponent');
        $otherModels = $tallComponent->getOtherModels()->toArray();
        $this->assertCount(2, $otherModels);
        $this->assertEquals(
            [
                'Ascsoftw\TallCrudGenerator\Tests\Models\Category', 
                'Ascsoftw\TallCrudGenerator\Tests\Models\Brand'
            ], 
            $otherModels
        );

        $this->assertEquals("\nuse App\Model\Brand;", $tallComponent->getUseModelCode('App\Model\Brand'));
    }
}
