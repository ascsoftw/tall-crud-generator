<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;

class SecondStepTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->step1();
    }

    public function test_default_settings()
    {

        $this->component
            ->assertSet('step', 2)
            ->assertSee('Previous')
            ->assertSee('Next')
            ->assertMethodWired('moveAhead')
            ->assertMethodWired('moveBack');

        $primaryKeyProps = $this->component->get('primaryKeyProps');
        $this->assertTrue($primaryKeyProps['inList']);
        $this->assertTrue($primaryKeyProps['sortable']);
        $this->assertEmpty($primaryKeyProps['label']);

        $componentProps = $this->component->get('componentProps');
        $this->assertTrue($componentProps['createAddModal']);
        $this->assertTrue($componentProps['createEditModal']);
        $this->assertTrue($componentProps['createDeleteButton']);
    }
}
