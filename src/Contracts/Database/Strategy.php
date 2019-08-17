<?php

namespace Laraform\Contracts\Database;

use Laraform\Contracts\Elements\Element;

interface Strategy
{
    /**
     * Load value to target
     *
     * @param object $target
     * @return void
     */
    public function load($target);

    /**
     * Fill value to target from data
     *
     * @param object $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true);

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target);

    /**
     * Return freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target);
}