<?php


namespace App\MessageHandler;


use App\Exception\MessengerException;
use App\MessageHandler\Registration\RegistrationHandler;
use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ArrayMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private RegistrationHandler $registrationHandler,
        private MessageBusInterface $messageBus
    ){}

    public function __invoke(ArrayMessage $message)
    {
        if(isset($message->getData()['action'])) {
            switch ($message->getData()['action']) {
                case 'register':
                    $handler = $this->registrationHandler;
                    break;
                default: $handler = null;
            }
            try {
                if($handler) $handler($message);
            } catch (MessengerException $e) {
                $responseMessage = json_decode($e->getMessage()) ?? $e->getMessage();

                $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
                    'code' => $e->getStatusCode(),
                    'error' => $responseMessage
                ]));
            }
        }
    }
}
