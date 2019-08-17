<?php

namespace Laraform\Authorization\Permission;

class IteratorFactory
{
    /**
     * Make new instance of Iterator
     *
     * @param array $roles
     * @return void
     */
    public function make(array $roles)
    {
        $permissions = app(Iterator::class);

        foreach ($roles as $role) {
            $permissions->add($role);
        }

        return $permissions;
    }
}