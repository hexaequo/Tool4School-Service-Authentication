<?php


namespace App\Exception\Authentication;


use App\Exception\MessengerException;

class BadCredentialsException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(400,'Bad credentials.');
    }
}
