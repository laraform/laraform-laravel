<?php

namespace Laraform\Authorization\Permission;

class Iterator
{ 
    /**
     * Current key of iterator
     *
     * @var integer
     */
    public $current = 0;

    /**
     * List of Permissions
     *
     * @var Permission[]
     */
    public $permissions = [];

    /**
     * Permission factory instance
     *
     * @var Factory
     */
    public $factory;

    /**
     * Return new Iterator instance
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Add role to permission
     *
     * @param string $role
     * @param callable $callback
     * @return void
     */
    public function add($role, callable $callback = null)
    {
        $this->permissions[] = $this->factory->make($role, $callback);
    }

    /**
     * Increment current key
     *
     * @return void
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * Determine if current key exists
     *
     * @return void
     */
    public function valid()
    {
        return array_key_exists($this->current, $this->permissions);
    }

    /**
     * Return current item
     *
     * @return void
     */
    public function current()
    {
        return $this->permissions[$this->current];
    }

    /**
     * Return total number of items
     *
     * @return void
     */
    public function count()
    {
        return count($this->permissions);
    }
}