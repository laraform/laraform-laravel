<?php

namespace Laraform\User;

class UserBuilder
{
    /**
     * Auth guard key
     *
     * @var string
     */
    private $guard;
    
    /**
     * Name of attribute which returns the roles on entity
     *
     * @var string
     */
    private $rolesAttribute;

    /**
     * Return new User instance
     *
     * @return Laraform\Contracts\User\User
     */
    public function build()
    {
        return app()->makeWith(User::class, [
            'builder' => $this
        ]);
    }

    /**
     * Get the value of guard
     */ 
    public function getGuard()
    {
        return $this->guard;
    }

    /**
     * Set the value of guard
     *
     * @return  self
     */ 
    public function setGuard($guard)
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Get the value of rolesAttribute
     */ 
    public function getRolesAttribute()
    {
        return $this->rolesAttribute;
    }

    /**
     * Set the value of rolesAttribute
     *
     * @return  self
     */ 
    public function setRolesAttribute($rolesAttribute)
    {
        $this->rolesAttribute = $rolesAttribute;

        return $this;
    }
}