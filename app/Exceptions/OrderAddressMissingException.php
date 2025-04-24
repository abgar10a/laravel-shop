<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;
use Illuminate\Http\Response;

class OrderAddressMissingException extends Exception
{
    public function __construct($message = "Order must have an address.", $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return ResponseHelper::error($this->message, Response::HTTP_BAD_REQUEST);
    }

//    public function report()
//    {
//        logger()->error($this->message);
//    }

    public function isValid(string $value): bool
    {
        try {
            logger()->info(11111111);
            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
