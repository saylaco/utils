<?php

namespace Sayla\Exception;

use Sayla\Contract\Exception;

class Error extends \Exception implements Exception
{
    use ErrorTrait;

    public function __construct($message, $previous = null, $code = null)
    {
        parent::__construct($message, $code ?? static::$classCode, $previous);
    }
}