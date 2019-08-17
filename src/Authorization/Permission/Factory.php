<?php

namespace Laraform\Authorization\Permission;

class Factory
{
    /**
     * Make new Permission instance
     *
     * @param string $role
     * @param callable $callback
     * @return Permission
     */
    public function make($role, callable $callback = null)
    {
        return new Permission($role, $callback);
    }
}