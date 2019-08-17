<?php

namespace Laraform\Support;

use Laraform\Contracts\Support\Arr as ArrContract;
use Illuminate\Support\Arr as IlluminateArr;

class Arr extends IlluminateArr implements ArrContract
{
    /**
     * Merges two array recursively
     * 
     * https://stackoverflow.com/a/25712428
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeDeep(&$array1, &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergeDeep($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged))
                    $merged[] = $value;
            } else
                $merged[$key] = $value;
        }

        return $merged;
    }

    /**
     * Converts an all objects in an array to array
     *
     * @param array $array
     * @return array
     */
    public static function forceArray($array) {
        return json_decode(json_encode($array), true);
    }
}