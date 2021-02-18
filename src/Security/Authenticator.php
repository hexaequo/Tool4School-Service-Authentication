<?php


namespace App\Security;


use App\Exception\Authentication\BadCredentialsException;
use App\Security\Authenticators\AuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;

class Authenticator
{
    public function __construct(private iterable $authenticators)
    {
    }

    public function authenticate(array $data) {
        /** @var AuthenticatorInterface $authenticator */
        foreach($this->authenticators as $authenticator) {
            if($authenticator->supports($data)) {
                $credentials = $authenticator->getCredentials($data);
                $user = $authenticator->getUser($credentials);
                if($authenticator->checkCredentials($credentials,$user)) return $user;
                else throw new BadCredentialsException();
            }
        }
        return null;
    }
}
