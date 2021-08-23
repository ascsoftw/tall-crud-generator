<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;

class FirstStepTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    public function test_default_settings()
    {
        Livewire::test(TallCrudGenerator::class)
            ->assertSet('step', 1)
            ->assertPropertyWired('modelPath')
            ->assertDontSee('Previous')
            ->assertSee('Next')
            ->assertMethodWired('moveAhead');
    }

    public function test_model_is_required()
    {
        Livewire::test(TallCrudGenerator::class)
            ->call('moveAhead')
            ->assertSet('step', 1)
            ->assertSee('Please enter Path to your Model')
            ->assertSet('isValidModel', false)
            ->assertHasErrors(['modelPath' => 'required']);
    }

    public function test_non_existent_file()
    {
        Livewire::test(TallCrudGenerator::class)
            ->set('modelPath', 'App\Models\NoModel')
            ->call('moveAhead')
            ->assertSet('step', 1)
            ->assertSee('File does not exists')
            ->assertSet('isValidModel', false);
    }

    public function test_file_which_is_not_model()
    {
        Livewire::test(TallCrudGenerator::class)
            ->set('modelPath', 'Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator')
            ->call('moveAhead')
            ->assertSet('step', 1)
            ->assertSee('Not a Valid Model Class.')
            ->assertSet('isValidModel', false);
    }

    public function test_valid_model_path()
    {
        $component = Livewire::test(TallCrudGenerator::class)
            ->set('modelPath', 'Ascsoftw\TallCrudGenerator\Tests\Models\Brand')
            ->call('moveAhead')
            ->assertSet('step', 2)
            ->assertSee('Previous')
            ->assertSet('isValidModel', true);
        
        $modelProps = $component->get('modelProps');
        $this->assertEquals('brands', $modelProps['tableName']);
        $this->assertEquals('id', $modelProps['primaryKey']);
        $this->assertCount(3, $modelProps['columns']);
        $this->assertContains('name', $modelProps['columns']);
        $this->assertContains('created_at', $modelProps['columns']);
        $this->assertContains('updated_at', $modelProps['columns']);
    }
}
