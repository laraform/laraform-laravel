<?php

namespace Laraform\Event;

use Laraform\Contracts\Event\Event as EventContract;

class Event implements EventContract
{
    /**
     * List of listeners
     *
     * @var array
     */
    public $listeners = [];

    /**
     * Makes a callback listen to an event
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public function listen($event, callable $callback)
    {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Fire and event
     *
     * @param string $event
     * @return void
     */
    public function fire($event)
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            call_user_func($listener);
        }
    }
}