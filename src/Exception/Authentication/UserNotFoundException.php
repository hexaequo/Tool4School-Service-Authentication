<?php


namespace App\Exception\Authentication;


use App\Exception\MessengerException;

class UserNotFoundException extends MessengerException
{
    public function __construct($username)
    {
        parent::__construct(404, sprintf('User "%s" was not found in database.',$username));
    }
}
