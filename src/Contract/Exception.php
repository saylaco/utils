<?php

namespace Sayla\Contract;

/**
 * @property array $context
 */
interface Exception extends \Throwable
{

    public function withContext($key, $data = null);

    public function withExtra(...$messages);
}