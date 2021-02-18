<?php


namespace App\Exception\Authentication;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;

class BadCredentialsException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_BAD_REQUEST,'Bad credentials.');
    }
}
