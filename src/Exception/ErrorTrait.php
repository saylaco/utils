<?php

namespace Sayla\Exception;

use Psr\Log\LoggerInterface;

trait ErrorTrait
{
    protected static $classCode = 0;
    protected static $log = true;
    private static $logger;
    public $context = [];
    protected $extra = [];

    /**
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    public function shouldLog()
    {
        return static::$log;
    }

    public function withContext($key, $data = null)
    {
        if (func_num_args() == 1) {
            $this->context[] = $key;
        } else {
            $this->context[$key] = $data;
        }
        return $this;
    }

    public function withErrorLog(\Throwable $exception = null, LoggerInterface $logger = null)
    {
        $logger = $logger ?? self::$logger;
        if (isset($logger)) {
            $exception = $exception ?? $this;
            $context = [
                'file' => $exception->getFile() . ':' . $exception->getLine(),
                'code' => $exception->getCode(),
                'context' => $this->context
            ];
            $logger->error(static::class . ': ' . $exception->getMessage(), $context);
            $logger->debug($exception->getTraceAsString());
        }
        return $this;
    }

    public function withExtra(...$messages)
    {
        $this->message .= ' ' . join('. ', $messages);
        return $this;
    }
}