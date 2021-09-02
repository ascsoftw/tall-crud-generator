<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\WithTemplates;

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

        $tallComponent = $this->component->get('tallComponent');
        $searchCode = $tallComponent->getSearchCode();

        $this->assertEquals(true, $tallComponent->getSearchingFlag());
        $this->assertEquals(['name'], $tallComponent->getSearchableColumns()->toArray());
        $this->assertEquals(WithTemplates::getSearchingVarsTemplate(), $tallComponent->getSearchVars());
        $this->assertEquals(WithTemplates::getSearchMethodTemplate(), $tallComponent->getSearchMethod());
        $whereClauseStr = <<<'EOT'
$query->where('name', 'like', '%' . $this->q . '%')
EOT;
        $this->assertEquals($whereClauseStr, $tallComponent->getSearchWhereClause()->toArray()[0]);
        $this->assertStringContainsString($whereClauseStr, $searchCode['query']);
        $this->assertNotEmpty($searchCode['query']);

    }

    // public function test_search_on_two_columns()
    // {

    // }
}
