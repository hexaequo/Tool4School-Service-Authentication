<?php


namespace App\MessageHandler\Registration;


use App\Entity\User;
use App\Messenger\ArrayMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationHandler
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

        $missingKeys = [];
        foreach(['username','password'] as $key) {
            if(!isset($data[$key])) { $missingKeys[] = $key; }
        }

        if(!empty($missingKeys)) {
            $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
                'code' => 422,
                'error' => [
                    'title' => 'Fields are missing in the request.',
                    'fields' => $missingKeys
                ]
            ]));
            return;
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,$data['password']));

        $violations = $this->validator->validate($user);

        if($violations->count() > 0) {
            $violationData = [];
            foreach($violations as $violation) {
                $violationData[] = ['message' => $violation->getMessage(),'field'=>$violation->getPropertyPath()];
            }
            $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
                'code' => 400,
                'error' => [
                    'title' => 'Request could not be handled because of violations.',
                    'violations' => $violationData
                ]
            ]));
            return;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new ArrayMessage($message->getId(), [
            'code' => 201,
            'id' => $user->getId()
        ]));
    }
}
