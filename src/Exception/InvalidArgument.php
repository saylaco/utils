<?php

namespace Sayla\Exception;

use Sayla\Contract\Exception;

class InvalidArgument extends \InvalidArgumentException implements Exception
{
    use ErrorTrait;

    public function __construct($message, $previous = null)
    {
        parent::__construct($message, null, $previous);
    }
}