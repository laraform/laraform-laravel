<?php

namespace Laraform\Support;

// use Laraform\Contracts\Support\Json as JsonContract;

class Json/* implements ArrContract*/
{
    public static function isJson($var)
    {
        if (!is_string($var)) {
            return false;
        }

        json_decode($var);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}