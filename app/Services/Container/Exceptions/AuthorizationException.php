<?php

namespace App\Services\Container\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
} 