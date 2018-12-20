<?php

namespace Sayla\Util;

abstract class Hints
{
    const HINTABLE = 'view';
    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';
    /**
     * The array of active item paths.
     *
     * @var array
     */
    protected $locations = [];

    /**
     * The array of items that have been located.
     *
     * @var array
     */
    protected $hintables = [];

    /**
     * The namespace to file path hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * Add a location to the finder.
     *
     * @param  string $location
     * @return void
     */
    public function addLocation($location)
    {
        $this->locations[] = $location;
    }

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string $namespace
     * @param  string|array $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $hints = (array)$hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->setNamespace($namespace, $hints);
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param  string $namespace
     * @param  string|array $hints
     * @return void
     */
    public function setNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array)$hints;
    }

    /**
     * Get the fully qualified location of the item.
     *
     * @param  string $name
     * @return string
     */
    public function exists($name)
    {
        try {
            return $this->find($name) !== false;
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    /**
     * Get the fully qualified location of the item.
     *
     * @param  string $name
     * @return string
     */
    public function find($name)
    {
        if (isset($this->hintables[$name])) {
            return $this->hintables[$name];
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->hintables[$name] = $this->findNamespaced($name);
        }

        return $this->hintables[$name] = $this->findInLocations($name, $this->locations);
    }

    /**
     * Returns whether or not the item name has any hint information.
     *
     * @param  string $name
     * @return bool
     */
    public function hasHintInformation($name)
    {
        return strpos($name, static::HINT_PATH_DELIMITER) > 0;
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string $name
     * @return string
     */
    protected function findNamespaced($name)
    {
        list($namespace, $_name) = $this->parseNamespaceSegments($name);

        return $this->findInLocations($_name, $this->hints[$namespace]);
    }

    /**
     * Get the segments of a template with a named path.
     *
     * @param  string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseNamespaceSegments($name)
    {
        $segments = explode(static::HINT_PATH_DELIMITER, $name);

        if (count($segments) != 2) {
            throw new \InvalidArgumentException(ucfirst(static::HINTABLE) . " [$name] has an invalid name.");
        }

        if (!isset($this->hints[$segments[0]])) {
            throw new \InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Find the given item in the list of paths.
     *
     * @param  string $name
     * @param  array $locations
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    abstract protected function findInLocations($name, $locations);

    /**
     * Flush the cache of located items.
     *
     * @return void
     */
    public function flush()
    {
        $this->hintables = [];
    }

    /**
     * Get the namespace to file path hints.
     *
     * @return array
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Get the active item paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->locations;
    }

    /**
     * Prepend a location to the finder.
     *
     * @param  string $location
     * @return void
     */
    public function prependLocation($location)
    {
        array_unshift($this->locations, $location);
    }

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string $namespace
     * @param  string|array $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints)
    {
        $hints = (array)$hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->setNamespace($namespace, $hints);
    }

}
