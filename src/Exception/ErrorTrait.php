<?php

namespace Sayla\Exception;

trait ErrorTrait
{
    public $context = [];
    protected $extra = [];

    public function withContext($key, $data = null)
    {
        if (func_num_args() == 1) {
            $this->context[] = $key;
        } else {
            $this->context[$key] = $data;
        }
        return $this;
    }

    public function withExtra(...$messages)
    {
        $this->message .= ' ' . join('. ', $messages);
        return $this;
    }
}