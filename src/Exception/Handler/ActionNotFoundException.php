<?php


namespace App\Exception\Handler;


use App\Exception\MessengerException;

class ActionNotFoundException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(422, '"action" key is missing.');
    }
}
