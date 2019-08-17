<?php

namespace Laraform\Authorization\Permission;

use Laraform\Contracts\Authorization\Permission as PermissionContract;
use Laraform\Contracts\User\User;

class Permission implements PermissionContract
{
    /**
     * Wildcard character for
     * allowing all roles
     *
     * @var string
     */
    const WILDCARD = '*';

    /**
     * Permitted role
     *
     * @var string
     */
    private $role;

    /**
     * Name of column on entity which
     * should contain the user's key
     *
     * @var string
     */
    private $userAttribute;

    /**
     * Callback to call if otherwise
     * the user if allowed to perform
     *
     * @var callable
     */
    private $callback;

    /**
     * Return new Permission instance
     *
     * @param string $role
     * @param callable $callback
     */
    public function __construct($role, callable $callback = null) {
        $this->role = $this->parseRole($role);
        $this->userAttribute = $this->parseUserAttribute($role);
        $this->callback = $callback;
    }

    /**
     * Determine if user is allowed to perform an action
     *
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    public function allowed(User $user, $entity = null)
    {
        if (!$this->isRoleAllowed($user) || !$this->isUserAllowed($user, $entity)) {
            return false;
        }

        return $this->hasCallback()
            ? $this->callback($user, $entity)
            : true;
    }

    /**
     * Determine if user's role is allowed to perform an action
     *
     * @param User $user
     * @return boolean
     */
    private function isRoleAllowed(User $user)
    {
        if ($this->role !== null
            && $this->role !== self::WILDCARD
            && !in_array($this->role, $user->roles())
        ) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user is allowed to perform an action
     *
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    private function isUserAllowed(User $user, $entity = null)
    {
        if ($this->userAttribute === null) {
            return true;
        }

        if (!$user->authenticated() || !$this->isOwner($user, $entity)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user is owner of entity
     *
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    private function isOwner(User $user, $entity)
    {
        return $entity->{$this->userAttribute} == $user->key();
    }

    /**
     * Determine if has callback
     *
     * @return boolean
     */
    private function hasCallback()
    {
        return $this->callback !== null;
    }

    /**
     * Call the callback
     *
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    private function callback(User $user, $entity)
    {
        return call_user_func_array($this->callback, [
            $user, $entity
        ]);
    }

    /**
     * Parse role
     *
     * @param string $role
     * @return void
     */
    private function parseRole($role)
    {
        return $role !== null ? explode(':', $role)[0] : null;
    }

    /**
     * Parse user attribute
     *
     * @param string $role
     * @return void
     */
    private function parseUserAttribute($role)
    {
        $parts = explode(':', $role);

        return isset($parts[1]) ? $parts[1] : null;
    }
}