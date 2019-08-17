<?php

namespace Laraform\Authorization;

class AuthorizationBuilder
{
    private $permissions;

    /**
     * Return new Authorization instance.
     */
    public function build()
    {
        return app()->makeWith(Authorization::class, [
            'builder' => $this
        ]); 
    }

    /**
     * Get the value of permissions
     */ 
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the value of permissions
     *
     * @return  self
     */ 
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }
}