<?php


namespace App\MessageHandler\Registration;


use App\Entity\User;
use App\MessageHandler\AbstractHandler;
use App\Messenger\ArrayMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationHandler extends AbstractHandler
{
    public function __construct(
      private EntityManagerInterface $entityManager,
      private MessageBusInterface $messageBus,
      private UserPasswordEncoderInterface $passwordEncoder,
      private ValidatorInterface $validator
    ){}

    public function __invoke(ArrayMessage $message)
    {
        $data = $message->getData();

        $this->checkFieldsMissing($message,['username','password']);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,$data['password']));

        $this->validate($this->validator, $user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
            'code' => 201,
            'Content-Location' => '/me'
        ]));
    }
}
