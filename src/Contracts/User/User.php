<?php

namespace Laraform\Contracts\User;

interface User
{
    /**
     * Determine if a user is logged in
     *
     * @return void
     */
    public function authenticated();

    /**
     * Return the User model
     *
     * @return Illuminate\Foundation\Auth\User
     */
    public function user();

    /**
     * Return the user's key
     *
     * @return int
     */
    public function key();

    /**
     * Return the user's roles if any
     *
     * @return array|null
     */
    public function roles();
}