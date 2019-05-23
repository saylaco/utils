<?php

namespace Sayla\Exception;

use Illuminate\Support\Str;

trait ErrorTrait
{
    public $context = [];
    protected $extra = [];

    protected static function appendPreviousMessage($message, $previous) {
        if ($previous) {
            return Str::finish($message, '. ') . $previous->getMessage();
        }
        return $message;
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

    public function appendMessage(...$messages)
    {
        $this->message .= ' ' . join('. ', $messages);
        return $this;
    }

    /**
     * @param mixed ...$messages
     * @deprecated use appendMessage
     */
    public function withExtra(...$messages)
    {
        return $this->appendMessage(...$messages);
    }
}