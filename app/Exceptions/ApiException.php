<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function render($request)
    {
        return response(['message' => $this->getMessage()], $this->getCode());
    }
}
