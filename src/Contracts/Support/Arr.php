<?php

namespace Laraform\Contracts\Support;

interface Arr
{
    /**
     * Merges two array recursively
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeDeep(&$array1, &$array2);
    
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($array, $key, $default = null);
}