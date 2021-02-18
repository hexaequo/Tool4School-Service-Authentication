<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MissingDataException extends MessengerException
{
    public function __construct(array $fields)
    {
        parent::__construct(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            json_encode([
                'title' => 'Fields are missing in the request.',
                'fields' => $fields
            ])
        );
    }
}
