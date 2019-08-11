<?php

namespace Sayla\Exception;

class RecordNotFound extends Error
{
    const ERROR_CODE = 0;
    protected static $defaultEntityName = 'Record';

    public function __construct($identifier, string $entityName = null, $previous = null)
    {
        parent::__construct($this->makeMessage($identifier, $entityName), static::ERROR_CODE, $previous);
    }

    /**
     * @param $identifier
     * @param null|string $entityName
     * @return string
     */
    protected function makeMessage($identifier, ?string $entityName): string
    {
        return sprintf(
            '%s record not found %s',
            is_string($identifier) ? $identifier : json_encode($identifier),
            $entityName ?: self::$defaultEntityName
        );
    }
}