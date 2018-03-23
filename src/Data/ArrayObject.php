<?php

namespace Sayla\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Sayla\Helper\Data\ArrayObject as BaseArrayObject;
use Sayla\Util\JsonHelper;

class ArrayObject extends BaseArrayObject implements Arrayable, Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return JsonHelper::encode($this->getArrayCopy(), $options);
    }
}