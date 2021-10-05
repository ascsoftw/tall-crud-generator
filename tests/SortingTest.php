<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\Template;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\TallProperties;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Illuminate\Support\Facades\App;

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
            ->finishStep1()
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
            ->finishStep1()
            ->disableModals()
            ->pressNext()
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->pressNext(3)
            ->disableFlashMessage()
            ->disablePaginationDropdown()
            ->pressNext()
            ->generateFiles();

        // $tallProperties = $this->component->get('tallProperties');
        // $componentCode = $this->component->get('componentCode');

        $tallProperties = App::make(TallProperties::class);
        $componentCode = App::make(ComponentCode::class);
        $sortCode = $componentCode->getSortCode();

        $this->assertTrue($tallProperties->isSortingEnabled());
        $this->assertEquals('id', $tallProperties->getDefaultSortableColumn());
        $this->assertEquals(Template::getSortingQuery(), $componentCode->getSortingQuery());
        $this->assertEquals(Template::getSortingMethod(), $componentCode->getSortingMethod());
        $this->assertNotEmpty($sortCode['vars']);

    }

    public function test_other_column_is_sortable()
    {
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
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
        $tallProperties = App::make(TallProperties::class);

        $this->assertNotEmpty($props['code']['sort']['vars']);
        $this->assertNotEmpty($props['code']['sort']['query']);
        $this->assertNotEmpty($props['code']['sort']['method']);

        $this->assertTrue($tallProperties->isSortingEnabled());
        $this->assertEquals('name', $tallProperties->getDefaultSortableColumn());
    }
}
