<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Exception;

class ApiExceptionHandler extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'error' => $this->getMessage()
        ], $this->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
