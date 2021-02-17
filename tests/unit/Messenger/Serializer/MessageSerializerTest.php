<?php


namespace App\Tests\unit\Messenger\Serializer;


use App\Messenger\ArrayMessage;
use App\Messenger\Serializer\MessageSerializer;
use App\Tests\unit\UnitTestCase;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;

class MessageSerializerTest extends UnitTestCase
{
    public function testDecode() {
        $encodedEnvelope = [
            'body' => '{
                "id": "123",
                "data": {
                    "test": true
                }
            }',
            'headers' => ['stamps' => serialize([new AmqpStamp('authentication')])]
        ];

        $serializer = self::$container->get(MessageSerializer::class);
        $result = $serializer->decode($encodedEnvelope);
        $this->assertInstanceOf(Envelope::class,$result);

        /** @var Envelope $result */
        $this->assertInstanceOf(ArrayMessage::class, $result->getMessage());

        /** @var ArrayMessage $message */
        $message = $result->getMessage();
        $this->assertEquals('123',$message->getId());
        $this->assertEquals(['test'=>true],$message->getData());

        $this->assertCount(1,$result->all());
        $this->assertArrayHasKey('Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp',$result->all());
        $this->assertInstanceOf(AmqpStamp::class,$result->all()['Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp'][0]);
    }

    public function testDecodeUnknownJson() {
        $encodedEnvelope = [
            'body' => '{
                "value": 123
            }',
            'headers' => []
        ];

        $serializer = self::$container->get(MessageSerializer::class);
        $result = $serializer->decode($encodedEnvelope);
        $this->assertInstanceOf(Envelope::class,$result);

        /** @var Envelope $result */
        $this->assertInstanceOf(ArrayMessage::class, $result->getMessage());

        /** @var ArrayMessage $message */
        $message = $result->getMessage();
        $this->assertEquals(['value'=>123],$message->getData());
    }

    public function testEncode() {
        $message = new ArrayMessage('abc',['test'=>123]);

        $envelope = new Envelope($message);
        $envelope = $envelope->with(... [new AmqpStamp('authentication')]);

        $serializer = self::$container->get(MessageSerializer::class);
        $encoded = $serializer->encode($envelope);

        $this->assertIsArray($encoded);
        $this->assertArrayHasKey('body',$encoded);
        $this->assertEquals(['id'=>'abc','data'=>['test'=>123]],json_decode($encoded['body'],true));

        $this->assertArrayHasKey('headers',$encoded);
        $this->assertArrayHasKey('stamps',$encoded['headers']);
        $this->assertCount(1,unserialize($encoded['headers']['stamps']));
        $this->assertInstanceOf(AmqpStamp::class,unserialize($encoded['headers']['stamps'])[0]);
    }
}
