<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

trait ListenerRegistryTrait
{
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
