<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function (ApiExceptionHandler $e, $request) {
            return $e->render($request);
        });
    }
}
