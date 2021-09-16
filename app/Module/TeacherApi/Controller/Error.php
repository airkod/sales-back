<?php

declare(strict_types = 1);

namespace App\Module\TeacherApi\Controller;

use Light\ErrorController;
use Light\Exception;

class Error extends ErrorController
{
    public function index()
    {
        $errors = [];

        $exception = $this->getException();

        if (is_subclass_of($exception, Exception::class) || ($exception instanceof Exception)) {
            $errors = $exception->getErrors();
        }

        if ($this->isExceptionEnabled()) {

            return [

                'status'  => $exception->getCode(),
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTrace(),

                'request' => [
                    'method'   => $this->getRequest()->getMethod(),
                    'get'      => $this->getRequest()->getGetAll(),
                    'post'     => $this->getRequest()->getPostAll(),
                    'x-headers'=> $this->getRequest()->getXHeaders()
                ],
                'errors'  => $errors,
            ];
        }

        return [
            'message' => $exception->getMessage(),
            'errors' => $errors
        ];
    }
}
