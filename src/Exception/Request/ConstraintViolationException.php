<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends MessengerException
{
    public function __construct(ConstraintViolationListInterface $violationList)
    {
        $violationData = [];
        foreach($violationList as $violation) {
            $violationData[] = ['message' => $violation->getMessage(),'field'=>$violation->getPropertyPath()];
        }

        parent::__construct(Response::HTTP_BAD_REQUEST,
            json_encode(['title'=>'Request could not be handled because of violations.','violations' => $violationData])
        );
    }

}
