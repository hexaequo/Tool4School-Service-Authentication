<?php


namespace App\Security\Authenticators;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    public function supports(array $data): bool;
    public function getCredentials(array $data): array;
    public function getUser($credentials): User;
    public function checkCredentials($credentials, User $user): bool;
}
