<?php

namespace Laraform\Database\Strategies;

use Laraform\Support\Arr;

class CollectionStrategy extends Strategy
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

    /**
     * Fill value to target from data
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true)
    {
        foreach ($this->children() as $child) {
            $child->fill($target, $data, $emptyOnNull);
        }
    }

    /**
     * Return freshly inserted keys
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return array
     */
    public function getNewKeys($target)
    {
        $keys = [];
        foreach ($this->children() as $child) {
            $childKeys = $child->getNewKeys($target);

            if (!empty($childKeys)) {
                $keys[$child->attribute] = $childKeys;
            }
        }

        return $keys;
    }
}