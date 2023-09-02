<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
interface ListenerRegistryInterface
{
    /**
     * Adds the given event listener to the list of event listeners
     * that listen to the given event.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     * @param int      $accepted_args
     *
     * @return bool
     */
    public function addListener(
        string $eventName,
        callable $listener,
        int $priority,
        int $accepted_args
    ): bool;

    /**
     * Removes the given event listener from the list of event listeners
     * that listen to the given event.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @return bool
     */
    public function removeListener(string $eventName, callable $listener, int $priority): bool;

    /**
     * Remove all the listener from an event.
     *
     * @param string $event_name
     * @param false|int $priority
     * @return bool
     */
    public function removeAllListener(string $event_name, $priority = false): bool;
}
