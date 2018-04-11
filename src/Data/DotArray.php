<?php namespace Sayla\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class DotArray extends ArrayObject
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(array $input = [])
    {
        $this->fill($input);
    }

    /**
     * @param array|\Traversable $input
     * @return $this
     */
    public function fill($input)
    {
        foreach ($input as $k => $v)
            $this->set($k, $v);
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->put($key, $value);
        return $this;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function put($offset, $value)
    {
        Arr::set($this->getArrayData(), $offset, $value);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param mixed $offset
     * @return DotArray
     */
    public function offsetGet($offset)
    {
        return Arr::get($this->getArrayData(), $offset);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @param string[] ...$keys
     * @return array
     */
    public function fetch(...$keys)
    {
        return $this->fetchArray($keys);
    }

    /**
     * @param array $keys
     * @return array
     */
    public function fetchArray(array $keys)
    {
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = $this->get($key);
        }
        return $out;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->offsetGet($key) ?? $default;
    }

    /**
     * @param array|\Traversable $input
     * @return $this
     */
    public function fillIf($input)
    {
        foreach (array_dot($input) as $k => $v)
            if (!$this->has($k)) {
                $this->set($k, $v);
            }
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    public function offsetExists($offset)
    {
        return Arr::has($this->getArrayData(), $offset);
    }

    /**
     * @return array
     */
    public function getCollapsed()
    {
        return Arr::collapse($this->getArrayData());
    }

    /**
     * @param int $depth
     * @return array
     */
    public function getFlattened($depth = null)
    {
        if (isset($depth)) {
            return Arr::flatten($this->getArrayData(), $depth);
        }
        return Arr::flatten($this->getArrayData());
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function getKeys($key = null, $default = [])
    {
        if (func_num_args() == 0) {
            return array_keys($this->getArrayData());
        }
        return array_keys($this->get($key, [])) ?: $default;
    }

    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $dataArray = [];
        foreach (parent::toArray() as $k => $v) {
            if ($v instanceof Arrayable) {
                $dataArray[$k] = $v->toArray();
            } else {
                $dataArray[$k] = $v;
            }
        }
        return $dataArray;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function merge(array $values)
    {
        $this->setArrayData(array_merge($this->getArrayData(), $values));
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function normalSet($key, $value)
    {
        $this->getArrayData()[$key] = $value;
        return $this;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /**
     * @param string $offset
     * @return $this
     */
    public function forget(string $offset)
    {
        Arr::forget($this->getArrayData(), $offset);
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function override(array $values)
    {
        $this->setArrayData(array_replace_recursive($this->getArrayData(), $values));
        return $this;
    }

    /**
     * @param $key
     * @param mixed $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->getArrayData(), $key, $default);
    }

    /**
     * @param $value
     * @return $this
     */
    public function push($value)
    {
        array_push($this->getArrayData(), $value);
        return $this;
    }

    /**
     * @param string $groupKey
     * @param mixed $item
     * @param string|null $itemKey
     * @return $this
     */
    public function pushUniqueTo($groupKey, $item, $itemKey = null)
    {
        $this->pushTo($groupKey, $item, $itemKey);
        $this->set($groupKey, array_unique($this->get($groupKey)));
        return $this;
    }

    /**
     * @param string $groupKey
     * @param mixed $item
     * @param string|null $itemKey
     * @return $this
     */
    public function pushTo($groupKey, $item, $itemKey = null)
    {
        $groupData = $this->get($groupKey, []);
        if (!is_null($itemKey)) {
            Arr::set($groupData, $itemKey, $item);
        } else {
            $groupData[] = $item;
        }
        $this->set($groupKey, $groupData);
        return $this;
    }

    /**
     * @param $groupKey
     * @param $value
     * @return $this
     */
    public function removeFrom($groupKey, $value, $itemKey = null)
    {
        $groupData = $this->get($groupKey, []);
        if (!is_null($itemKey)) {
            unset($groupData[$itemKey]);
            $this->set($groupKey, $groupData);
        } else {
            $index = array_search($value, $groupData);
            if ($index !== false) {
                unset($groupData[$index]);
                $this->set($groupKey, array_values($groupData));
            }
        }
        return $this;
    }

    public function toDottedArray()
    {
        return Arr::dot($this->toArray());
    }
}