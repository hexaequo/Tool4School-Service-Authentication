<?php

namespace App\Security\Authenticators;

use App\Entity\User;
use App\Exception\Authentication\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

class JWTAuthenticator implements AuthenticatorInterface
{
    private JWTEncoderInterface $JWTEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(JWTEncoderInterface $JWTEncoder, EntityManagerInterface $entityManager)
    {
        $this->JWTEncoder = $JWTEncoder;
        $this->entityManager = $entityManager;
    }

    public function supports(array $data): bool
    {
        return isset($data['Bearer']);
    }

    public function getCredentials(array $data): array
    {
        return [
            'token' => $data['Bearer']
        ];
    }

    public function getUser($credentials): User
    {
        $data = $this->JWTEncoder->decode($credentials['token']);

        $username = $data['username'];

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);

        if(!$user) {
            throw new UserNotFoundException($data['username']);
        }

        return $user;
    }

    public function checkCredentials($credentials, User $user): bool
    {
        return true;
    }
}
