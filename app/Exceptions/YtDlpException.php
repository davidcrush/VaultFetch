<?php

namespace App\Exceptions;

use Exception;

class YtDlpException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $processOutput = '',
    ) {
        parent::__construct($message);
    }
}
