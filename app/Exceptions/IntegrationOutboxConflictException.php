<?php

namespace App\Exceptions;

use RuntimeException;

class IntegrationOutboxConflictException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Identitas aggregate outbox sudah digunakan dengan payload berbeda.');
    }
}
