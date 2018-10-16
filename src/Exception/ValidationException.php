<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 16/10/18
 * Time: 20:03
 */

namespace App\Exception;


use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{
    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $message = [];
        /**
         * @var $violation ConstraintViolationInterface
         */
        foreach ($constraintViolationList as $violation){
            $message[$violation->getPropertyPath()] = $violation->getMessage();
        }
        parent::__construct(400, json_encode($message));
    }

}