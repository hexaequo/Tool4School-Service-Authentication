<?php


namespace App\Tests\unit\Messenger;

use App\Messenger\ArrayMessage;
use App\Tests\unit\UnitTestCase;

class ArrayMessageTest extends UnitTestCase
{
    public function testArrayMessageDataIsCorrect() {
        $message = new ArrayMessage('testid',['value'=>'test']);

        $this->assertEquals($message->getId(),'testid');
        $this->assertEquals($message->getData(),['value'=>'test']);
    }
}
