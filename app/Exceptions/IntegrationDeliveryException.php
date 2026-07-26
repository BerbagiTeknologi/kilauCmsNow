<?php

namespace App\Exceptions;

use RuntimeException;

class IntegrationDeliveryException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        public readonly bool $retryable,
        public readonly ?int $httpStatus = null,
    ) {
        parent::__construct('Pengiriman integrasi gagal.');
    }
}
