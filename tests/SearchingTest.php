<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class SearchingTest extends TestCase
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
            ->set('fields.0.searchable', true)
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles()
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);
    }

    public function test_search_is_setup()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->set('fields.0.searchable', true)
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles()
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);

        $props = $this->component->get('props');

        $this->assertNotEmpty($props['code']['search']['vars']);
        $this->assertNotEmpty($props['code']['search']['query']);
        $this->assertNotEmpty($props['code']['search']['method']);

        $this->component->call('isSearchingEnabled')
                ->assertReturnEquals('isSearchingEnabled', true);

        $this->component->call('getSearchableColumns')
                ->assertReturnCount('getSearchableColumns', 1);
    }
}
