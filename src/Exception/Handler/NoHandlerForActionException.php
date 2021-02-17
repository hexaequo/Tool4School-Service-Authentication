<?php


namespace App\Exception\Handler;


use App\Exception\MessengerException;

class NoHandlerForActionException extends MessengerException
{
    public function __construct(string $action)
    {
        parent::__construct(400, 'No handler found for action "'.$action.'".');
    }
}
