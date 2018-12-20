<?php

namespace Sayla\Util;

use Sayla\Exception\FileNotFoundException;
use Sayla\Exception\JsonError;

class JsonHelper
{

    /**
     * @param $path
     * @param bool $returnArrayOnNewFile
     * @return array
     * @throws FileNotFoundException
     */
    public static function decodeFileToArray($path, $returnArrayOnNewFile = false): array
    {
        return self::decodeFile($path, $returnArrayOnNewFile, true);
    }

    /**
     * @param $path
     * @param bool $returnArrayOnNewFile
     * @param bool $assoc
     * @return array|mixed
     * @throws FileNotFoundException
     */
    public static function decodeFile($path, $returnArrayOnNewFile = false, $assoc = false)
    {
        if (file_exists($path)) {
            return static::decode(file_get_contents($path), $assoc);
        }
        if ($returnArrayOnNewFile) return [];
        throw new FileNotFoundException($path);
    }

    /**
     * @param $json
     * @param bool $assoc
     * @return mixed
     */
    public static function decode($json, $assoc = false)
    {
        $result = json_decode($json, $assoc);

        if ($result || json_last_error() == JSON_ERROR_NONE) {
            return $result;
        }
        throw new JsonError(json_last_error());
    }

    /**
     * @param mixed $json
     * @return array
     */
    public static function decodeToArray($json): array
    {
        return self::decode($json, true) ?? [];
    }

    /**
     * @param $value
     * @param int $options
     * @return array
     */
    public static function encodeDecode($value, $options = 0)
    {
        return self::decode(self::encode($value, $options), false);
    }

    public static function encode($value, $options = 0)
    {
        $result = json_encode($value, $options);

        if (json_last_error() == JSON_ERROR_NONE) {
            return $result;
        }

        throw new JsonError(json_last_error());
    }

    /**
     * @param $value
     * @param int $options
     * @return array
     */
    public static function encodeDecodeToArray($value, $options = 0)
    {
        return self::decode(self::encode($value, $options), true);
    }

    /**
     * @param $path
     * @param $data
     * @param int $options
     * @param int $fileFlags
     * @return int
     */
    public static function encodeFile($path, $data, $options = JSON_PRETTY_PRINT, $fileFlags = null)
    {
        $contents = static::encode($data, $options);
        return file_put_contents($path, $contents, $fileFlags);
    }

}