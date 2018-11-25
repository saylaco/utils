<?php

namespace Sayla\Util\Mixin;

use Sayla\Helper\Data\BaseHashMap;

class MixinSet extends BaseHashMap
{
    protected $aliases = [];
    private $callableMethods = [];

    public static function fromObject($object, array $methods): self
    {
        $set = new self();
        $set->add($object, $methods);
        return $set;
    }

    public function add(Mixin $item, array $methods = null)
    {
        $this->put(class_basename($item), $item, $methods);
    }

    /**
     * @param string $name
     * @param \Sayla\Util\Mixin\Mixin $item
     * @param array|string[] $methods
     */
    public function put(string $name, Mixin $item, array $methods = null)
    {
        $this->items[$name] = $item;
        if (!isset($methods)) {
            $methods = get_class_methods($item);
        }
        foreach ($methods as $i => $methodName) {
            if (starts_with($methodName, '__') && $methodName != '__invoke') continue;
            $methodKey = !is_numeric($i) ? $i : $methodName;
            if ($methodKey != $methodName) {
                $this->aliases[$name][$methodKey] = $methodName;
            }
            $this->callableMethods[$methodKey] = $name;
        }
    }

    public function addMethod(string $name, callable $callback)
    {
        $this->put($name, MixinMethod::make($callback), [$name => '__invoke']);
    }

    public function addStaticMethods(string $item, string $pattern)
    {
        foreach (get_class_methods($item) as $methodName)
            if (str_is($pattern, $methodName)) {
                $this->addMethod($methodName, [$item, $methodName]);
            }
    }

    public function addMatchingMethods($item, string $pattern)
    {
        foreach (get_class_methods($item) as $methodName)
            if (str_is($pattern, $methodName)) {
                $this->addMethod($methodName, [$item, $methodName]);
            }
    }

    public function call(string $methodName, array $arguments)
    {
        if (str_contains($methodName, '_')) {
            [$mixinName, $methodName] = str_split($methodName, '_');
        } elseif (isset($this->callableMethods[$methodName])) {
            $mixinName = $this->callableMethods[$methodName];
        } else {
            throw new \BadMethodCallException('Mixin not found - ' . $methodName);
        }
        $realMethodName = $this->aliases[$mixinName][$methodName] ?? $methodName;
        return call_user_func_array([$this[$mixinName], $realMethodName], $arguments);
    }

    protected function get(string $methodName): Mixin
    {
        return $this->items[$methodName];
    }

    public function getCallableMethods(): array
    {
        return array_keys($this->callableMethods);
    }

    /**
     * @return \ArrayIterator|\Traversable|Mixin[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}