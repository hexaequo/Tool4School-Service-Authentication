<?php


namespace App\MessageHandler\User;


use App\MessageHandler\AbstractHandler;
use App\Security\Authenticator;

class MeHandler extends AbstractHandler
{
    public function __construct(private Authenticator $authenticatorHandler)
    {
    }

    public function __invoke(array $data)
    {
        $this->checkFieldsMissing($data,['Bearer']);

        $user = $this->authenticatorHandler->authenticate($data);

        return [
            'code' => 200,
            'id' => $user->getId(),
            'username' => $user->getUsername()
        ];
    }
}
