<?php

namespace Laraform\Authorization;

use Laraform\Authorization\Permission\IteratorFactory;
use Laraform\Contracts\Authorization\Authorization as AuthorizationContract;
use Laraform\Contracts\User\User;

class Authorization implements AuthorizationContract
{
    /**
     * List of permission iterators
     *
     * @var Laraform\Permission\Iterator[]
     */
    private $permissions = [];

    /**
     * List of permittable actions
     *
     * @var array
     */
    private $permittable = [
        'load', 'insert', 'update',
    ];

    /**
     * Return a new Authorization instance
     *
     * @param AuthorizationBuilder $builder
     * @param IteratorFactory $factory
     */
    public function __construct(AuthorizationBuilder $builder, IteratorFactory $factory) {
        $this->factory = $factory;

        foreach ($this->permittable as $action) {
            $this->permissions[$action] = $this->makePermissions($action, $builder->getPermissions());
        }
    }

    /**
     * Determine if a user is allowed to perform an action
     *
     * @param string $action
     * @param User $user
     * @param object $entity
     * @return void
     */
    public function authorize($action, User $user, $entity = null)
    {
        if (!in_array($action, $this->permittable)) {
            throw new \InvalidArgumentException("Unknown action: $action");
        }

        if (!$this->hasPermissions()) {
            return true;
        }

        return $this->can($action, $user, $entity);
    }

    /**
     * Permit the user to perform action
     *
     * @param string $action
     * @param string $role
     * @param callable $callback
     * @return void
     */
    public function permit($action, $role = null, callable $callback = null)
    {
        $this->permissions[$action]->add($role, $callback);
    }

    /**
     * Determine if a user can perform an action
     *
     * @param string $action
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    private function can($action, User $user, $entity = null)
    {
        while ($this->permissions[$action]->valid()) {
            if ($this->permissions[$action]->current()->allowed($user, $entity)) {
                return true;
            }

            $this->permissions[$action]->next();
        }

        return false;
    }

    /**
     * Determine if a user can't perform an action
     *
     * @param string $action
     * @param User $user
     * @param object $entity
     * @return boolean
     */
    private function cant($action, User $user, $entity = null)
    {
        return !$this->can($action, $user, $entity);
    }

    /**
     * Determine if there are any permissins set
     *
     * @return boolean
     */
    private function hasPermissions()
    {
        foreach ($this->permittable as $permittable) {
            if ($this->permissions[$permittable]->count() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create new Iterator instance
     *
     * @param string $action
     * @param array $permissions
     * @return Laraform\Permission\Iterator;
     */
    private function makePermissions($action, $permissions)
    {
        return $this->factory->make($this->createRoles($action, $permissions));
    }

    /**
     * Create roles array
     *
     * @param string $action
     * @param array $permissions
     * @return array
     */
    private function createRoles($action, $permissions)
    {
        if (in_array($action, $permissions)) {
            return ['*'];
        } elseif (array_key_exists($action, $permissions)) {
            return $permissions[$action];
        }

        return [];
    }
}