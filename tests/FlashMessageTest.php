<?php

namespace Ascsoftw\TallCrudGenerator\Tests;

use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Livewire\Livewire;

class FlashMessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        $this->component = Livewire::test(TallCrudGenerator::class)
            ->finishStep1()
            ->pressNext()
            ->setStandardFields()
            ->pressNext(3);
    }

    public function test_flash_message_is_enabled_by_default()
    {
        $this->component
            ->assertPropertyWired('flashMessages.enable')
            ->assertSet('flashMessages.enable', true);
    }

    public function test_flash_message_can_be_disabled()
    {
        $this->component
            ->assertPropertyWired('flashMessages.enable')
            ->set('flashMessages.enable', false)
            ->pressNext()
            ->generateFiles();

        $tallProperties = $this->component->get('tallProperties');
        $this->assertFalse($tallProperties->isFlashMessageEnabled());
    }

    public function test_flash_message_code_is_generated()
    {
        $this->component
            ->pressNext()
            ->generateFiles();
        $tallProperties = $this->component->get('tallProperties');
        $childComponentCode = $this->component->get('childComponentCode');

        $this->assertTrue($tallProperties->isFlashMessageEnabled());
        $flashCodeStr = <<<'EOT'
$this->emitTo('livewire-toast', 'show', 'Record Added Successfully');
EOT;
        $this->assertStringContainsString($flashCodeStr, $childComponentCode->getAddFlashCode());

        $this->assertStringContainsString($tallProperties->getFlashMessageText('add'), $childComponentCode->getAddFlashCode());
        $this->assertStringContainsString($tallProperties->getFlashMessageText('edit'), $childComponentCode->getEditFlashCode());
        $this->assertStringContainsString($tallProperties->getFlashMessageText('delete'), $childComponentCode->getDeleteFlashCode());
    }

    public function test_empty_flash_message()
    {
        $this->component
            ->assertPropertyWired('flashMessages.enable')
            ->set('flashMessages.text.add', '')
            ->pressNext()
            ->generateFiles();
        $tallProperties = $this->component->get('tallProperties');
        $childComponentCode = $this->component->get('childComponentCode');

        $this->assertTrue($tallProperties->isFlashMessageEnabled());
        $this->assertEmpty($childComponentCode->getAddFlashCode());
        $this->assertNotEmpty($childComponentCode->getEditFlashCode());
        $this->assertNotEmpty($childComponentCode->getDeleteFlashCode());
    }
}
