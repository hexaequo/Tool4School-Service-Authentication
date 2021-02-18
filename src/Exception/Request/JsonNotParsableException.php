<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;

class JsonNotParsableException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            'Request body can not be parsed to json.'
        );
    }
}
