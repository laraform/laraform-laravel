<?php

namespace Laraform\Contracts\Database;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

interface Relationship
{
    /**
     * Create new relationship
     *
     * @param integer $value
     * @param object $entity
     * @return Model
     */
    public function create($value, $entity);

    /**
     * Save relation for entity
     *
     * @param Model $entity
     * @return void
     */
    public function save(Model $entity);

    /**
     * Prepare value for loading
     *
     * @param array $value - array containing current element value
     * @param string $attribute - key of element on value
     * @return array
     */
    public function prepare($value, $attribute);

    /**
     * Decrypt key
     *
     * @param string $key
     * @return integer
     */
    public function decrypt($key);
}