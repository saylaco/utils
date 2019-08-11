<?php

namespace Sayla\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Sayla\Contract\Scriptable;
use Sayla\Util\Raw;

class JavascriptObject extends DotArrayObject implements Scriptable, Responsable
{
    private $forceToObject = null;

    /**
     * @param array $data
     * @return string
     */
    public static function convertToJavascript($data, bool $isObject = false): string
    {
        if ($data instanceof Raw) {
            return $data->getValue();
        }
        if ($data instanceof Scriptable) {
            return $data->toJavascript();
        }
        if ($data instanceof Jsonable) {
            return $data->toJson();
        }
        if ($data instanceof \JsonSerializable) {
            return self::convertToJavascript($data->jsonSerialize());
        }
        if ($data instanceof Arrayable) {
            return self::convertToJavascript($data->toArray());
        }
        $data = simple_value($data);
        if (is_string($data)) {
            return starts_with($data, ['[', '{', '\'', '"', 'false', 'true', 'null', 'undefined'])
                ? $data
                : '"' . $data . '"';
        }
        if (is_iterable($data)) {
            if ($isObject == true) {
                $isArray = false;
            } else {
                $isArray = null;
            }
            $js = [];
            foreach ($data as $k => $v) {
                if ($isArray === null) {
                    $isArray = is_numeric($k);
                }
                $v = self::convertToJavascript($v);
                $js[] = $isArray ? $v : $k . ': ' . $v;
            }
            return ($isArray ? '[' : '{') . join(', ', $js) . ($isArray ? ']' : '}');
        }
        return var_str($data);
    }

    public function __toString()
    {
        return $this->toJavascript();
    }

    /**
     * @param bool $forceToObject
     * @return $this
     */
    public function forceToObject(bool $forceToObject = true)
    {
        $this->forceToObject = $forceToObject;
        return $this;
    }

    public function setObject($key, $data)
    {
        parent::set($key, $object = new JavascriptObject($data));
        return $object;
    }

    public function setRaw($key, $data)
    {
        parent::set($key, new Raw($data));
        return $this;
    }

    public function toJavascript(): string
    {
        return self::convertToJavascript($this->getArrayData(), $this->forceToObject ?? false);
    }

    public function toResponse($request)
    {
        return new \Illuminate\Http\JsonResponse($this);
    }
}