<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    private $data;
    private int $statusCode;

    public function __construct(string $message, $data = null, int $statusCode = 422)
    {
        parent::__construct($message);
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
