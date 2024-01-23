<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function __construct($message = "Something went wrong", $code = 500,)
    {
        parent::__construct($message,$code);
    }
}
