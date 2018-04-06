<?php

namespace Sayla\Exception;

use Sayla\Contract\Exception;

class JsonError extends \InvalidArgumentException implements Exception
{
    use ErrorTrait;
    protected static $_messages = [
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    ];

    public function __construct(int $error, $previous = null)
    {
        parent::__construct(static::$_messages[$error], null, $previous);
        $this->withContext('json_error_code', $error);
    }
}