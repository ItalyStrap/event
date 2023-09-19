<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

trait ListenerRegisterTrait
{
    /**
     * Right now is only an experimental method, an idea to fetch $eventName
     * from the listener first argument using reflection.
     * I make this private for now to avoid to deprecated it in the future.
     */
    private function addListenerFromCallable(
        callable $listener,
        string $eventName = null,
        int $priority = 10,
        int $accepted_args = 3
    ): bool {
        if (null === $eventName) {
            return false;
        }

        return add_filter($eventName, $listener, $priority, $accepted_args);
    }

    public function addListener(
        string $eventName,
        callable $listener,
        int $priority = 10,
        int $accepted_args = 3
    ): bool {
        return add_filter($eventName, $listener, $priority, $accepted_args);
    }

    public function removeListener(
        string $eventName,
        callable $listener,
        int $priority = 10
    ): bool {
        return remove_filter($eventName, $listener, $priority);
    }

    public function removeAllListener(string $eventName, int $priority = null): bool
    {
        return remove_all_filters($eventName, $priority);
    }

    public function hasListener(string $eventName, $callback = false)
    {
        return has_filter($eventName, $callback);
    }
}
