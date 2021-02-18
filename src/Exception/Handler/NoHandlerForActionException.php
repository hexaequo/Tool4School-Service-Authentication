<?php


namespace App\Exception\Handler;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;

class NoHandlerForActionException extends MessengerException
{
    public function __construct(string $action)
    {
        parent::__construct(Response::HTTP_NOT_IMPLEMENTED, 'No handler found for action "'.$action.'".');
    }
}
