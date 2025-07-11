<?php

namespace App\Services\Container\Exceptions;

use Exception;

class ContainerLabUpdateException extends Exception
{
    public function __construct(string $message, ?Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
