<?php


namespace App\Tests\integration\MessageHandler\Registration;


use App\MessageHandler\ArrayMessageHandler;
use App\Messenger\ArrayMessage;
use App\Messenger\TestArrayMessageIn;
use App\Tests\functional\FunctionalTestCase;
use App\Tests\integration\IntegrationTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class RegistrationHandlerTest extends IntegrationTestCase
{
    public function testGoodRequest() {
        $id = uniqid('testRegister_');
        $arrayMessage = new ArrayMessage(
            $id,
            [
                'username' => 'testUsername',
                'password' => 'testPassword',
                'action' => 'register'
            ]
        );

        (self::$container->get(ArrayMessageHandler::class))($arrayMessage);

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.main');
        $this->assertCount(1,$transport->getSent());
        $response = $transport->getSent()[0];
        $this->assertInstanceOf(ArrayMessage::class,$response->getMessage());

        /** @var ArrayMessage $message */
        $message = $response->getMessage();

        $this->assertEquals($id,$message->getId());
        $this->assertEquals([
            'code' => 201,
            'Content-Location' => '/me'
        ],$message->getData());
    }

    public function testNoAction() {
        $id = uniqid('testRegister_');
        $arrayMessage = new ArrayMessage(
            $id,
            [
                'username' => 'testUsername',
                'password' => 'testPassword'
            ]
        );

        (self::$container->get(ArrayMessageHandler::class))($arrayMessage);

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.main');
        $this->assertCount(1,$transport->getSent());
        $response = $transport->getSent()[0];
        $this->assertInstanceOf(ArrayMessage::class,$response->getMessage());

        /** @var ArrayMessage $message */
        $message = $response->getMessage();

        $this->assertEquals($id,$message->getId());
        $this->assertEquals([
            'code' => 422,
            'error' => '"action" key is missing.'
        ],$message->getData());
    }

    public function testNoHandlerForAction() {
        $id = uniqid('testRegister_');
        $arrayMessage = new ArrayMessage(
            $id,
            [
                'username' => 'testUsername',
                'password' => 'testPassword',
                'action' => 'randomAction'
            ]
        );

        (self::$container->get(ArrayMessageHandler::class))($arrayMessage);

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.main');
        $this->assertCount(1,$transport->getSent());
        $response = $transport->getSent()[0];
        $this->assertInstanceOf(ArrayMessage::class,$response->getMessage());

        /** @var ArrayMessage $message */
        $message = $response->getMessage();

        $this->assertEquals($id,$message->getId());
        $this->assertEquals([
            'code' => 400,
            'error' => 'No handler found for action "randomAction".'
        ],$message->getData());
    }

    public function testMissingField() {
        $id = uniqid('testRegister_');
        $arrayMessage = new ArrayMessage(
            $id,
            [
                'username' => 'testUsername',
                'action' => 'register'
            ]
        );

        (self::$container->get(ArrayMessageHandler::class))($arrayMessage);

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.main');
        $this->assertCount(1,$transport->getSent());
        $response = $transport->getSent()[0];
        $this->assertInstanceOf(ArrayMessage::class,$response->getMessage());

        /** @var ArrayMessage $message */
        $message = $response->getMessage();

        $this->assertEquals($id,$message->getId());
        $this->assertEquals([
            'code' => 422,
            'error' => [
                'title' => 'Fields are missing in the request.',
                'fields' => ['password']
            ]
        ],$message->getData());
    }

    public function testValidationConstraint() {
        $id = uniqid('testRegister_');
        $arrayMessage = new ArrayMessage(
            $id,
            [
                'username' => 'the',
                'password' => 'testpassword',
                'action' => 'register'
            ]
        );

        (self::$container->get(ArrayMessageHandler::class))($arrayMessage);

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.main');
        $this->assertCount(1,$transport->getSent());
        $response = $transport->getSent()[0];
        $this->assertInstanceOf(ArrayMessage::class,$response->getMessage());

        /** @var ArrayMessage $message */
        $message = $response->getMessage();

        $this->assertEquals($id,$message->getId());
        $this->assertEquals([
            'code' => 400,
            'error' => [
                'title' => 'Request could not be handled because of violations.',
                'violations' => [[
                    'field' => 'username',
                    'message' => 'This value is too short. It should have 4 characters or more.'
                ]]
            ]
        ],$message->getData());
    }
}
