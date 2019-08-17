<?php

namespace Laraform\Support;

use Hashids\Hashids;

class Hash
{
    /**
     * Encode value
     *
     * @param mixed $value
     * @return string
     */
    public static function encode($value)
    {
        return (new Hashids(md5(env('APP_KEY')), 10))->encode($value);
    }

    /**
     * Decode value
     *
     * @param mixed $value
     * @return string
     */
    public static function decode($value)
    {
        return (new Hashids(md5(env('APP_KEY')), 10))->decode($value)[0];
    }
}