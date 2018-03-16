<?php

namespace Sayla\Util\Factory;

abstract class Factory
{
    protected $resolvers = [];

    protected $instances = [];

    protected $resolverNotFoundError = 'Resolver not found - %s';

    protected function addInstance(string $name, $resolvedInstance)
    {
        return $this->instances[$name] = $resolvedInstance;
    }

    protected function addResolver(\Closure $resolver, string $name = null)
    {
        if ($name == null) {
            $this->resolvers[] = $resolver;
        } else {
            $this->resolvers[$name] = $resolver;
        }
    }

    protected function getInstance(string $name)
    {
        if (!$this->hasInstance($name)) {
            throw new \ErrorException(sprintf($this->resolverNotFoundError, $name));
        }
        return $this->instances[$name];
    }

    public function isSupported(string $name): bool
    {
        return $this->hasInstance($name) || $this->hasResolver($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasInstance(string $name): bool
    {
        return isset($this->instances[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasResolver(string $name): bool
    {
        return isset($this->resolvers[$name]);
    }

    public function requireResolver(string $name): \Closure
    {
        $resolver = $this->getResolver($name);
        if ($resolver == null) {
            throw new \ErrorException(sprintf($this->resolverNotFoundError, $name));
        }
        return $resolver;
    }

    public function getResolver(string $name): ?\Closure
    {
        if ($this->hasResolver($name)) {
            return $this->resolvers[$name];
        }
        return null;
    }

    protected function resolve($resolver, ...$args)
    {
        return call_user_func_array($resolver, $args);
    }

}