<?php

namespace Sayla\Util\Mixin;

final class MixinMethod implements Mixin
{
    /** @var callable */
    private $callback;

    private function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public static function make($callback): self
    {
        return new self($callback);
    }

    public function __invoke()
    {
        return call_user_func_array($this->callback, func_get_args());
    }
}