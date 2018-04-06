<?php

namespace Sayla\Exception;

use Sayla\Contract\Exception;

class FileNotFoundException extends \UnexpectedValueException implements Exception
{
    use ErrorTrait;

    public function __construct($fileName, $previous = null)
    {
        parent::__construct(sprintf("File '%s' not found", $fileName), null, $previous);
    }
}