<?php

namespace Laraform\Contracts\Authorization;

use Laraform\Contracts\User\User;

interface Authorization
{
    /**
     * Determine if a user is allowed to perform an action
     *
     * @param string $action
     * @param User $user
     * @param object $entity
     * @return void
     */
    public function authorize($action, User $user, $entity);

    /**
     * Permit the user to perform action
     *
     * @param string $action
     * @param string $role
     * @param callable $callback
     * @return void
     */
    public function permit($action, $role = null, callable $callback = null);
}