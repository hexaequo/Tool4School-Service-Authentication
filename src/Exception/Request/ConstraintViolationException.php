<?php


namespace App\Exception\Request;


use App\Exception\MessengerException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends MessengerException
{
    public function __construct(ConstraintViolationListInterface $violationList)
    {
        $violationData = [];
        foreach($violationList as $violation) {
            $violationData[] = ['message' => $violation->getMessage(),'field'=>$violation->getPropertyPath()];
        }

        parent::__construct(400,
            json_encode(['title'=>'Request could not be handled because of violations.','violations' => $violationData])
        );
    }

}
