<?php

namespace Laraform\User;

use Laraform\Contracts\User\User as UserContract;

class User implements UserContract
{
    /**
     * Auth guard name
     *
     * @var string|null
     */
    public $guard;

    /**
     * Name of roles attribute on user's model
     *
     * @var array|null
     */
    public $rolesAttribute;

    /**
     * Return new User instance
     *
     * @param UserBuilder $builder
     * @param \Auth $auth
     */
    public function __construct(UserBuilder $builder, \Auth $auth)
    {
        $this->auth = $auth;

        $this->guard = $builder->getGuard();
        $this->rolesAttribute = $builder->getRolesAttribute();
    }

    /**
     * Determine if the user is logged in
     *
     * @return void
     */
    public function authenticated()
    {
        return $this->auth()::check();
    }

    /**
     * Get the User model
     *
     * @return Illuminate\Foundation\Auth\User
     */
    public function user()
    {
        return $this->auth()::user();
    }

    /**
     * Get the user key
     *
     * @return int
     */
    public function key()
    {
        return $this->user()->getKey();
    }

    /**
     * Get the user role
     *
     * @return array|null
     */
    public function roles()
    {
        $user = $this->user();

        if ($user !== null) {
            $roles = $user->{$this->rolesAttribute};

            return is_array($roles) ? $roles : [$roles];
        }
    }

    /**
     * Return the Auth object
     *
     * @return Illuminate\Support\Facades\Auth
     */
    private function auth()
    {
        return $this->guard !== null ? $this->auth::guard($this->guard) : $this->auth;
    }
}