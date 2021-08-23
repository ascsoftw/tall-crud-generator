<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\TallCrudGeneratorServiceProvider;
use Livewire\LivewireServiceProvider;
use Christophrumpel\MissingLivewireAssertions\MissingLivewireAssertionsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup

        // Brand::insert([
        //     [ 'id' => 1, 'name' => 'Brand Two', ],
        //     [ 'id' => 2, 'name' => 'Brand One', ],
        // ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            TallCrudGeneratorServiceProvider::class,
            LivewireServiceProvider::class,
            MissingLivewireAssertionsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
        include_once __DIR__.'/database/migrations/create_test_tables.php.stub';
        (new \CreateTestTables())->up();
    }
}
