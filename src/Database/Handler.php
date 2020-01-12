<?php

namespace Laraform\Database;

use Illuminate\Support\Facades\DB;
use Laraform\Contracts\Database\Handler as HandlerContract;
use Laraform\Contracts\Elements\Element;
use Laraform\Support\Arr;

class Handler implements HandlerContract
{
    /**
     * Elements composition
     *
     * @var Laraform\Contracts\Elements\Element
     */
    private $elements;

    /**
     * Name of model class
     *
     * @var string
     */
    private $model;

    /**
     * Current entity
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $entity;

    /**
     * Database class
     *
     * @var DB
     */
    private $db;

    /**
     * Return new Handler instance
     *
     * @param string $model
     * @param DB $db
     */
    public function __construct($model, DB $db)
    {
        $this->model = $model;
        $this->db = $db;
    }

    /**
     * Find an entity by key
     *
     * @param integer $key
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($key)
    {
        return $this->entity = $this->model()->find($key);
    }

    /**
     * Return data of an entity
     *
     * @param integer $key
     * @return array
     */
    public function load($key)
    {
        $this->find($key);

        return $this->retrive();
    }

    /**
     * Insert data
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data)
    {
        $this->db::transaction(function() use ($data) {
            $this->create();
            $this->fill($data);
            $this->save();
        });
    }

    /**
     * Update data
     *
     * @param array $data
     * @param integer $key
     * @param boolean $emptyOnNull
     * @return void
     */
    public function update(array $data, $key, $emptyOnNull = true)
    {
        $this->db::transaction(function () use ($data, $key, $emptyOnNull) {
            $this->find($key);
            $this->fill($data, $emptyOnNull);
            $this->save();
        });
    }

    /**
     * Get freshly inserted keys
     *
     * @return array
     */
    public function getNewKeys()
    {
        return $this->elements->getNewKeys($this->entity);
    }

    /**
     * Set value of elements
     *
     * @param Element $elements
     * @return void
     */
    public function setElements(Element $elements)
    {
        $this->elements = $elements;
    }
    
    /**
     * Returns current entity
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Create and set new entity
     *
     * @return void
     */
    private function create()
    {
        $this->entity = $this->model();
    }

    /**
     * Save entity
     *
     * @return void
     */
    private function save()
    {
        $this->entity->save();
    }

    /**
     * Retrive data from elements
     *
     * @return void
     */
    private function retrive()
    {
        return Arr::forceArray($this->elements->load($this->entity));
    }

    /**
     * Fill elements with data
     *
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    private function fill($data, $emptyOnNull = true)
    {
        $this->elements->fill($this->entity, $data, $emptyOnNull);
    }

    /**
     * Return model instance
     *
     * @return void
     */
    private function model()
    {
        return app($this->model);
    }
}