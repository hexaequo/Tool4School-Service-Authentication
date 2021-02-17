<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;

class JsonNotParsableException extends MessengerException
{
    public function __construct()
    {
        parent::__construct(
            400,
            'Request body can not be parsed to json.'
        );
    }
}
