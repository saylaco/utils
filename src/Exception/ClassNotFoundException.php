<?php

namespace Sayla\Exception;

class ClassNotFoundException extends \ErrorException
{
    public function __construct(string $className = null, $code = 0, $severity = 1, $filename = __FILE__,
                                $lineno = __LINE__,
                                \Exception $previous = null)
    {
        $message = empty($className) ? 'Class name was not provided' : 'Class not found - ' . $className;
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}