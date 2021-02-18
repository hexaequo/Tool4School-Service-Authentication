<?php


namespace App\Exception\Authentication;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends MessengerException
{
    public function __construct($username)
    {
        parent::__construct(Response::HTTP_NOT_FOUND, sprintf('User "%s" was not found in database.',$username));
    }
}
