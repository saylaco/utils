<?php


use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

if (!function_exists('make_new_instance')) {
    /**
     * @param $class
     * @param array ...$constructorArgs
     * @return object
     */
    function make_new_instance($class, ...$constructorArgs)
    {
        return (new \ReflectionClass($class))->newInstanceArgs($constructorArgs);
    }
}
if (!function_exists('str_wrap')) {
    function str_wrap(iterable $items, string $start, string $end): string
    {
        return $start . join($end . $start, $items) . $end;
    }
}
if (!function_exists('query_str')) {
    /**
     * @param \Sayla\Contract\SqlBuilder $builder
     * @return string
     */
    function query_str($builder): string
    {
        $bindings = array_map('var_str', $builder->getBindings());
        return str_replace_array('?', $bindings, $builder->toSql());
    }
}
if (!function_exists('html_str')) {
    function html_str(...$markup): HtmlString
    {
        if (func_num_args() == 1 && is_array($markup[0])) {
            $markup = $markup[0];
        }
        $html = join(PHP_EOL, $markup);
        return new HtmlString($html);
    }
}
if (!function_exists('html_str_from_markdown')) {
    function html_str_from_markdown($markup): HtmlString
    {
        return html_str(Parsedown::instance()->text($markup));
    }
}

if (!function_exists('array_rekey')) {
    function array_rekey($items, $prefix)
    {
        $newKeys = array_map(function ($key) use ($prefix) {
            return value($prefix) . $key;
        }, array_keys($items));
        return array_combine($newKeys, $items);
    }
}

if (!function_exists('var_str')) {
    /**
     * Runs var_export against the data but converts arrays to the short array syntax
     * @param mixed $data
     * @return string
     */
    function var_str(...$data)
    {
        $dumps = [];
        foreach ($data as $dataItem) {
            if (is_bool($dataItem)) {
                $dump = $dataItem ? 'true' : 'false';
            } elseif ($dataItem === null) {
                $dump = 'null';
            } else {
                $dump = \var_export($dataItem, true);
            }

            $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
            $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
            $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
            if (gettype($dataItem) == 'object') { // Deal with object states
                $dump = str_replace('__set_state(array(', '__set_state([', $dump);
                $dumps[] = preg_replace('#\)\)$#', "])", $dump);
            } else {
                $dumps[] = preg_replace('#\)$#', "]", $dump);
            }
        }

        return join(' ', $dumps);
    }
}

if (!function_exists('convert_backslashes')) {
    function convert_backslashes($string, $replacement = DIRECTORY_SEPARATOR)
    {
        // trim and return the path
        return str_replace('\\', $replacement, trim($string, ' \\'));
    }
}
if (!function_exists('trim_paths')) {
    function trim_paths($paths, $rootPath): iterable
    {
        if (!is_iterable($paths)) {
            $paths = [$paths];
        }
        return array_map(function ($file) use ($rootPath) {
            return trim_path($file, $rootPath);
        }, $paths);
    }
}
if (!function_exists('trim_path')) {
    function trim_path($path, $rootPath): string
    {
        return str_replace($rootPath, '', $path);
    }
}
if (!function_exists('variable_hash')) {
    function variable_hash($value): string
    {
        return is_object($value) ? spl_object_hash($value) : md5(serialize($value));
    }
}
if (!function_exists('random_variable_hash')) {
    function random_variable_hash($value): string
    {
        return sha1(variable_hash($value) . time() . rand());
    }
}
if (!function_exists('str_title')) {
    function str_title($value): string
    {
        static $conversions;
        return $conversions[$value]
            ?? $conversions[$value] = ucfirst(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $value));
    }
}
if (!function_exists('carbon')) {
    function carbon($value = null): Carbon
    {
        if (is_array($value)) {
            return Carbon::create(...$value);
        }
        if (is_string($value)) {
            return new Carbon($value);
        }
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }
        return Carbon::create();
    }
}
if (!function_exists('capture_output')) {
    function capture_output(callable $callable): string
    {
        ob_start();
        call_user_func($callable);
        return ob_get_clean() ?: '';
    }
}