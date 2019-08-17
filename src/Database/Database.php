<?php

namespace Laraform\Database;

use Laraform\Database\DatabaseBuilder;

class Database
{
    /**
     * Form instance
     *
     * @var Laraform\Laraform
     */
    private $form;

    /**
     * Database handler instance
     *
     * @var Handler
     */
    private $handler = Handler::class;

    /**
     * Creates new database instance
     *
     * @param DatabaseBuilder $builder
     */
    public function __construct(DatabaseBuilder $builder)
    {
        $this->form = $builder->getForm();
        $this->handler = $this->makeHandler();
    }

    /**
     * Find entity by key
     *
     * @param integer $key
     * @return object
     */
    public function find($key)
    {
        return $this->getHandler()->find($key);
    }

    /**
     * Load data by key
     *
     * @param integer $key
     * @return array
     */
    public function load($key)
    {
        return $this->getHandler()->load($key);
    }

    /**
     * Insert data
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data)
    {
        return $this->getHandler()->insert($data);
    }

    /**
     * Update entity with data
     *
     * @param array $data
     * @param integer $key
     * @param bool $emptyOnNull
     * @return void
     */
    public function update(array $data, $key, $emptyOnNull = true)
    {
        return $this->getHandler()->update($data, $key, $emptyOnNull);
    }

    /**
     * Return freshly inserted keys
     *
     * @return array
     */
    public function getNewKeys()
    {
        return $this->getHandler()->getNewKeys();
    }

    /**
     * Return database entity
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->getHandler()->getEntity();
    }

    /**
     * Return current Handler instance
     *
     * @return Handler
     */
    protected function getHandler()
    {
        $this->setElements();

        return $this->handler;
    }

    /**
     * Set elements for Handler
     *
     * @return void
     */
    protected function setElements()
    {
        $this->handler->setElements($this->form->getElements());
    }

    /**
     * Create new Database Handler instance
     *
     * @return Laraform\Contracts\Database\Handler
     */
    protected function makeHandler()
    {
        return app()->makeWith($this->handler, [
            'model' => $this->form->model,
        ]);
    }
}