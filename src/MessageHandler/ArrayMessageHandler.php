<?php


namespace App\MessageHandler;


use App\MessageHandler\Registration\RegistrationHandler;
use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ArrayMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private RegistrationHandler $registrationHandler
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

            if($handler) $handler($message);
        }
    }
}
