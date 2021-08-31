<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;

class SortTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext(2);
    }

    public function test_listing_can_be_reordered()
    {
        $this->component
            ->assertSeeInOrder(['Listing', 'Add Fields', 'Edit Fields'])
            ->call('showSortDialog', 'listing')
            ->assertSet('sortingMode', 'listing')
            ->assertSeeInOrder(['id', 'name', 'price', 'sku'])
            ->call('reorder', ['id', 'sku', 'name', 'price'])
            ->assertSeeInOrder(['id', 'sku', 'name', 'price']);

    }

}
