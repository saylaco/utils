<?php

namespace Sayla\Util\Mixin;

use Sayla\Helper\Data\BaseHashMap;

class MixinSet extends BaseHashMap
{
    private $callableMethods = [];

    public function add(Mixin $item)
    {
        $this->put(class_basename($item), $item);
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
        return call_user_func_array([$this[$mixinName], $methodName], $arguments);
    }

    /**
     * @return \ArrayIterator|\Traversable|Mixin[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function getMixinMethods()
    {
        $methods = [];
        /** @var \ReflectionClass[] $reflectors */
        $reflectors = [];
        foreach ($this->callableMethods as $methodName => $mixinName) {
            if (!isset($reflectors[$mixinName])) {
                $reflectors[$mixinName] = new \ReflectionClass($this[$mixinName]);
            }
            $mixinReflection = $reflectors[$mixinName]->getMethod($methodName);
            $parameters = [];
            foreach ($mixinReflection->getParameters() as $reflectionParameter) {
                $parameters[] = [
                    'name' => $reflectionParameter->getName(),
                    'type' => $reflectionParameter->hasType()
                        ? qualify_var_type($reflectionParameter->getType()->getName())
                        : null,
                    'optional' => $reflectionParameter->isOptional()
                ];
            }
            $methods[] = [
                'name' => $methodName,
                'class' => $reflectors[$mixinName]->getName(),
                'methodName' => $mixinReflection->getName(),
                'mixinName' => $mixinName,
                'parameters' => $parameters,
                'returnType' => $mixinReflection->hasReturnType()
                    ? qualify_var_type($mixinReflection->getReturnType()->getName())
                    : null,
            ];
        }
        return $methods;
    }

    public function getMixinNames()
    {
       return array_keys($this->items);
    }

    public function put(string $name, Mixin $item)
    {
        $this->items[$name] = $item;
        $methods = get_class_methods($item);
        $prefixPos = array_search('getMixinMethodPrefix', $methods);
        if ($prefixPos !== false) {
            $prefix = array_pull($methods, $prefixPos);
        }
        foreach ($methods as $methodName) {
            if (isset($prefix) && !starts_with($methodName, $prefix)) continue;
            if (starts_with($methodName, '__')) continue;

            $this->callableMethods[$methodName] = $name;
        }
    }
}