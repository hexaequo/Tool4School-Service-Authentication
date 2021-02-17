<?php


namespace App\MessageHandler;


use App\Exception\Handler\ActionNotFoundException;
use App\Exception\Handler\NoHandlerForActionException;
use App\Exception\MessengerException;
use App\MessageHandler\Registration\RegistrationHandler;
use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
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
        try {
            if(isset($message->getData()['action'])) {
                switch ($message->getData()['action']) {
                    case 'register':
                        $handler = $this->registrationHandler;
                        break;
                    default: throw new NoHandlerForActionException($message->getData()['action']);
                }
                $handler($message);
            }
            else {
                throw new ActionNotFoundException();
            }
        } catch (MessengerException $e) {
            $responseMessage = json_decode($e->getMessage(),true) ?? $e->getMessage();

            $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
                'code' => $e->getStatusCode(),
                'error' => $responseMessage
            ]));
        }
    }
}
