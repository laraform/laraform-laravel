<?php

namespace Laraform\Database\Strategies;

class DefaultStrategy extends Strategy
{
    /**
     * Load value to target
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return void
     */
    public function load($target)
    {
        return [
            $this->name() => $target[$this->attribute()]
        ];
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
        $target[$this->attribute()] = $data[$this->name()];
    }

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        $target[$this->attribute()] = null;
    }

    /**
     * Return freshly inserted keys
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return array
     */
    public function getNewKeys($target)
    {
        if ($this->attribute() == $target->getKeyName() && $target->wasRecentlyCreated) {
            return $target[$this->attribute()];
        }
    }
}