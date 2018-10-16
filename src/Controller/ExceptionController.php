<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 16/10/18
 * Time: 21:47
 */

namespace App\Controller;


use App\Exception\ValidationException;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController
{
    use ControllerTrait;

    public function show(Request $request, $exception, DebugLoggerInterface $logger = null){
        if($exception instanceof ValidationException){
            return $this->getView(
                $exception->getStatusCode(),
                json_decode($exception->getMessage(),true)
            );
        }
        if($exception instanceof HttpException){
            return $this->getView(
                $exception->getStatusCode(),
                $exception->getMessage()
            );
        }
        return $this->getView(
            null,
            "Exception non Intercepter"
        );
    }
    private function getView(?int $statusCode, $message):View
    {
        $data = [
            'code'=> $statusCode ?? 500, //isset $statusCode return $statusCode else return 500
            'message' => $message
        ];
        return $this->view($data, $statusCode ?? 500);
    }

}