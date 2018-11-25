<?php namespace Sayla\Data;

use Sayla\Helper\Data\BaseHashMap;
use Sayla\Helper\Data\Contract\AccessArrayItemsAsObjectProperties;

class DotArrayObjects extends BaseHashMap
{
    use AccessArrayItemsAsObjectProperties;

    /**
     * DotArrayHash constructor.
     * @param \Sayla\Data\DotArrayObject[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $k => $v) {
            $this->put($k, is_iterable($v) ? new DotArrayObject($v) : $v);
        }
    }

    public function get($offset)
    {
        if (!$this->offsetExists($offset)) {
            $value = new DotArrayObject();
            $this->put($offset, $value);
            return $value;
        }
        return $this->items[$offset];
    }

    /**
     * @return \ArrayIterator|\Traversable|\Sayla\Data\DotArrayObject[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function put(string $offset, DotArrayObject $value)
    {
        $this->items[$offset] = $value;
    }
}