<?php

namespace Laraform\Contracts\Database;

interface Database
{
    /**
     * Find entity by key
     *
     * @param integer $key
     * @return object
     */
    public function find($key);

    /**
     * Load data by key
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
     * Update entity with data
     *
     * @param integer $key
     * @param array $data
     * @return void
     */
    public function update(array $data, $key);

    /**
     * Return freshly inserted keys
     *
     * @return array
     */
    public function getNewKeys();
}