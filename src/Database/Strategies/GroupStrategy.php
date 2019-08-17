<?php

namespace Laraform\Database\Strategies;

use Laraform\Support\Arr;

class GroupStrategy extends CollectionStrategy
{
    /**
     * Load value to target
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return void
     */
    public function load($target)
    {
        $data = [];

        foreach ($this->children() as $child) {
            $data = array_merge($data, $child->load($target));
        }

        return $data;
    }
}