<?php

namespace Sayla\Data;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sayla\Support\Raw;

class JavascriptObject extends DotArray
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
        if ($data instanceof self) {
            return $data->toJavascript();
        }
        if (is_string($data)) {
            return starts_with($data, ['[', '{', '\'', '"', 'false', 'true', 'null']) ? $data : '"' . $data . '"';
        }
        if (is_object($data) || Arr::accessible($data)) {
            if ($isObject == true) {
                $isArray = false;
            } else {
                $isArray = null;
            }
            $js = [];
            if ($data instanceof Collection) {
                return $data->toJson();
            }
            if ($data instanceof \JsonSerializable) {
                $data = $data->jsonSerialize();
            }
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

    public function toJavascript()
    {
        return self::convertToJavascript($this->getArrayData(), $this->forceToObject ?? false);
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
}