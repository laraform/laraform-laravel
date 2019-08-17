<?php

namespace Laraform\Contracts\Authorization;

use Laraform\Contracts\User\User;

interface Permission
{
    /**
     * Return new Permission instance
     *
     * @param string $role
     * @param callable $callback
     */
    public function __construct($role, callable $callback = null);

    /**
     * Determine if user is allowed to perform an action
     *
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    public function allowed(User $user, $entity = null);
}