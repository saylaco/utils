<?php

namespace Sayla\Util\Factory;

use Illuminate\Contracts\Container\Container;

abstract class BaseDriverFactory extends Factory
{
    protected static $activeDefault;
    protected $autoResolver;
    protected $defaults = [];
    protected $container;
    protected $configs = [];

    /**
     * StoreStrategyFactory constructor.
     * @param Container $container
     * @param array $configs
     */
    public function __construct(Container $container, $configs = [])
    {
        $this->container = $container;
        foreach ($configs as $name => $config)
            $this->addConfig($name, $config);
    }

    public function addConfig($name, $config)
    {
        $this->configs[$name] = $config;
        $this->addResolver(function ($name) use ($config) {
            $config['name'] = $name;
            return $this->makeDriver($config);
        }, $name);
    }

    abstract protected function makeDriver(array $config);

    public static function setDefault(string $strategyName)
    {
        static::$activeDefault = $strategyName;
    }

    public function get(string $name)
    {
        if ($this->hasInstance($name)) {
            return $this->getInstance($name);
        }
        if (!$this->hasResolver($name)) {
            if (isset($this->autoResolver)) {
                $resolved = $this->resolve($this->autoResolver, $name);
            }
            if (isset(static::$activeDefault)) {
                $resolved = $this->resolve($this->requireResolver(static::$activeDefault), $name);
            }
        }
        if (!isset($resolved)) {
            $resolved = $this->resolve($this->requireResolver($name), $name);
        }
        $this->addInstance($name, $resolved);
        return $resolved;
    }

    public function setAutoResolver(\Closure $autoResolver)
    {
        $this->autoResolver = $autoResolver;
        return $this;
    }

}