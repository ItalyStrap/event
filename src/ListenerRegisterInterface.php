<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
interface ListenerRegisterInterface
{
    public const PRIORITY = 10;
    public const ACCEPTED_ARGS = 5;

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
     * @param string $eventName
     * @param false|int $priority
     * @return bool
     */
    public function removeAllListener(string $eventName, $priority = false): bool;

    /**
     * Checks the WordPress plugin API to see if the given event has
     * the given callback. The priority of the callback will be returned
     * or false. If no callback is given will return true or false if
     * there's any callbacks registered to the event.
     *
     * @param string        $eventName               The name of the event name.
     * @param array|callable|false|string $callback Optional. The callback to check for. Default false.
     * @return bool|int If $function_to_check is omitted, returns boolean for whether the event has
     *                   anything registered. When checking a specific function, the priority of that
     *                   event is returned, or false if the function is not attached. When using the
     *                   $function_to_check argument, this function may return a non-boolean value
     *                   that evaluates to false (e.g.) 0, so use the === operator for testing the
     *                   return value.
     */
    public function hasListener(string $eventName, $callback = false);
}
