<?php


namespace App\MessageHandler\User;


use App\MessageHandler\AbstractHandler;
use App\Security\Authenticator;
use Symfony\Component\HttpFoundation\Response;

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
            'code' => Response::HTTP_OK,
            'id' => $user->getId(),
            'username' => $user->getUsername()
        ];
    }
}
