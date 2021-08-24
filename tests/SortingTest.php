<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;

class SortingTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->set('modelPath', 'Ascsoftw\TallCrudGenerator\Tests\Models\Product')
            ->call('moveAhead')
            ->set('primaryKeyProps.sortable', true)
            ->set('componentProps.createAddModal', false)
            ->set('componentProps.createEditModal', false)
            ->set('componentProps.createDeleteButton', false)
            ->call('moveAhead')
            ->call('addField')
            ->set('fields.0.column', 'name')
            ->call('moveAhead')
            ->call('moveAhead')
            ->call('moveAhead')
            ->set('flashMessages.enable', false)
            ->set('advancedSettings.table_settings.showPaginationDropdown', false)
            ->call('moveAhead');
    }

    public function test_component_is_generated()
    {

        $this->component
            ->set('componentName', 'products')
            ->call('moveAhead')
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);

    }

    public function test_sorting_code()
    {
        $this->component
            ->set('componentName', 'products')
            ->call('moveAhead');

        $props = $this->component->get('props');

        $this->assertNotEmpty($props['code']['sort']['vars']);
        $this->assertNotEmpty($props['code']['sort']['query']);
        $this->assertNotEmpty($props['code']['sort']['method']);

        $this->component->call('isPrimaryKeySortable')
            ->assertReturnEquals('isPrimaryKeySortable', true);

        $this->component->call('getDefaultSortableColumn')
            ->assertReturnEquals('getDefaultSortableColumn', 'id');

    }
}
