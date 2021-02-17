<?php


namespace App\MessageHandler;


use App\Exception\Request\ConstraintViolationException;
use App\Exception\Request\MissingDataException;
use App\Messenger\ArrayMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractHandler
{
    abstract public function __invoke(ArrayMessage $message);

    public function checkFieldsMissing(ArrayMessage $message, array $fields) {
        foreach ($fields as $key => $field) {
            if(isset($message->getData()[$field])) {
                unset($fields[$key]);
            }
        }
        $missingFields = [];
        foreach ($fields as $field) {
            $missingFields[] = $field;
        }
        if(!empty($missingFields)) throw new MissingDataException($missingFields);
    }

    public function validate(ValidatorInterface $validator, $entity) {
        $violationList = $validator->validate($entity);
        if($violationList->count() > 0) throw new ConstraintViolationException($violationList);
    }
}
