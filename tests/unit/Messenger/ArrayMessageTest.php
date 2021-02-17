<?php


namespace App\Tests\unit\Messenger;

use App\Messenger\ArrayMessage;
use App\Tests\unit\UnitTestCase;

class ArrayMessageTest extends UnitTestCase
{
    public function testArrayMessageDataIsCorrect() {
        $message = new ArrayMessage('testid',['value'=>'test']);

        $this->assertEquals('testid',$message->getId());
        $this->assertEquals(['value'=>'test'],$message->getData());

        $message->setData(['newValue'=>'abc']);

        $this->assertEquals(['newValue'=>'abc'],$message->getData());
    }

    public function testDates() {
        $message = new ArrayMessage('testid',['value'=>'test']);

        $sentDate = new \DateTime();
        $message->setSentAt($sentDate);
        $this->assertEquals($sentDate,$message->getSentAt());

        $startedDate = new \DateTime();
        $message->setStartedAt($startedDate);
        $this->assertEquals($startedDate,$message->getStartedAt());

        $endedDate = new \DateTime();
        $message->setEndedAt($endedDate);
        $this->assertEquals($endedDate,$message->getEndedAt());

        $receivedDate = new \DateTime();
        $message->setReceivedAt($receivedDate);
        $this->assertEquals($receivedDate,$message->getReceivedAt());
    }
}
