<?php

namespace Laraform\Contracts\Event;

interface Event
{
    /**
     * Make a callback listen to an event
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public function listen($event, callable $callback);

    /**
     * Fire and event
     *
     * @param string $event
     * @return void
     */
    public function fire($event);
}