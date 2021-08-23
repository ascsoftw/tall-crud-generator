<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;

class ListingOnlyComponentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->set('modelPath', 'Ascsoftw\TallCrudGenerator\Tests\Models\Product')
            ->call('moveAhead')
            ->set('componentProps.createAddModal', false)
            ->set('componentProps.createEditModal', false)
            ->set('componentProps.createDeleteButton', false)
            ->call('moveAhead')
            ->call('addAllFields')
            ->call('moveAhead')
            ->call('moveAhead')
            ->call('moveAhead')
            ->call('moveAhead');
    }

    public function test_default_settings()
    {

        $this->component
            ->assertSet('step', 7)
            ->assertSee('Previous')
            ->assertDontSee('Next')
            ->assertMethodWired('moveBack')
            ->assertPropertyWired('componentName');
    }

    public function test_component_name_is_required()
    {

        $this->component
            ->call('moveAhead')
            ->assertSee('Please enter the name of your component')
            ->assertHasErrors(['componentName' => 'required']);
    }

    public function test_component_is_generated()
    {

        $this->component
            ->set('componentName', 'products')
            ->call('moveAhead')
            ->assertSet('exitCode', 0)
            ->assertSet('isComplete', true);
        $generatedCode = $this->component->get('generatedCode');
        $this->assertStringContainsString('@livewire', $generatedCode);
        $this->assertEquals("@livewire('products')", $generatedCode);

        // $props = $this->component->get('props');
    }


}
