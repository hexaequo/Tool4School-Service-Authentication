<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MissingDataException extends MessengerException
{
    public function __construct(array $fields)
    {
        parent::__construct(
            422,
            json_encode([
                'title' => 'Fields are missing in the request.',
                'fields' => $fields
            ])
        );
    }
}
