<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;

class SortingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    public function test_component_is_generated()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles()
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);
    }

    public function test_primary_key_is_sortable()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles();

        $tallComponent = $this->component->get('tallComponent');
        $sortCode = $tallComponent->getSortCode();

        $this->assertEquals(true, $tallComponent->getSorting());
        $this->assertEquals('id', $tallComponent->getDefaultSortableColumn());
        $this->assertEquals(WithTemplates::getSortingQueryTemplate(), $tallComponent->getSortingQuery());
        $this->assertEquals(WithTemplates::getSortingMethodTemplate(), $tallComponent->getSortingMethod());
        $this->assertNotEmpty($sortCode['vars']);

        $this->component->call('isPrimaryKeySortable')
            ->assertReturnEquals('isPrimaryKeySortable', true);

    }

    public function test_other_column_is_sortable()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->makePrimaryKeyUnsortable()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.sortable', true)
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles();

        $props = $this->component->get('props');

        $this->assertNotEmpty($props['code']['sort']['vars']);
        $this->assertNotEmpty($props['code']['sort']['query']);
        $this->assertNotEmpty($props['code']['sort']['method']);

        $this->component->call('isSortingEnabled')
            ->assertReturnEquals('isSortingEnabled', true);

        $this->component->call('isPrimaryKeySortable')
            ->assertReturnEquals('isPrimaryKeySortable', false);

        $this->component->call('getDefaultSortableColumn')
            ->assertReturnEquals('getDefaultSortableColumn', 'name');
    }
}
