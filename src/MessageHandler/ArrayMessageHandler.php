<?php


namespace App\MessageHandler;


use App\Exception\Handler\ActionNotFoundException;
use App\Exception\Handler\NoHandlerForActionException;
use App\Exception\MessengerException;
use App\MessageHandler\Authentication\GetJWTHandler;
use App\MessageHandler\Registration\RegistrationHandler;
use App\Messenger\ArrayMessage;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ArrayMessageHandler implements MessageHandlerInterface
{
    private ArrayMessage $message;

    public function __construct(
        private RegistrationHandler $registrationHandler,
        private GetJWTHandler $getJWTHandler,
        private MessageBusInterface $messageBus
    ){}

    public function __invoke(ArrayMessage $message)
    {
        try {
            $this->message = $message;
            $this->message->setStartedAt(new \DateTime());
            $messageData = $this->message->getData();
            if(isset($messageData['action'])) {
                $handler = match ($messageData['action']) {
                    'register' => $this->registrationHandler,
                    'get_jwt' => $this->getJWTHandler,
                    default => throw new NoHandlerForActionException($messageData['action']),
                };

                $response = $handler($messageData);
                $this->sendMessage($response);
            }
            else {
                throw new ActionNotFoundException();
            }
        } catch (MessengerException $e) {
            $responseMessage = json_decode($e->getMessage(),true) ?? $e->getMessage();

            $this->sendMessage([
                'code' => $e->getStatusCode(),
                'error' => $responseMessage
            ]);
        }
    }

    public function sendMessage(array $data) {
        $this->message->setData($data);
        $this->message->setEndedAt(new \DateTime());
        $this->messageBus->dispatch($this->message);

    }
}
