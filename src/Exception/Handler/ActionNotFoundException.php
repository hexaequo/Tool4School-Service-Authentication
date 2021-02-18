<?php


namespace App\Exception\Handler;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;

class ActionNotFoundException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, '"action" key is missing.');
    }
}
