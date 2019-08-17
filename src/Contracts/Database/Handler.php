<?php

namespace Laraform\Contracts\Database;

use Laraform\Contracts\Elements\Element;

interface Handler
{
    /**
     * Find an entity by key
     *
     * @param integer $key
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($key);

    /**
     * Return data of an entity
     *
     * @param integer $key
     * @return array
     */
    public function load($key);

    /**
     * Insert data
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data);

    /**
     * Update data
     *
     * @param array $data
     * @param integer $key
     * @param boolean $emptyOnNull
     * @return void
     */
    public function update(array $data, $key, $emptyOnNull);

    /**
     * Get freshly inserted keys
     *
     * @return array
     */
    public function getNewKeys();

    /**
     * Set value of elements
     *
     * @param Element $elements
     * @return void
     */
    public function setElements(Element $elements);

    /**
     * Returns current entity
     *
     * @return mixed
     */
    public function getEntity();
}