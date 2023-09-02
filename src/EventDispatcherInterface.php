<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
interface EventDispatcherInterface extends ListenerRegistryInterface
{
    /**
     * Executes all the callbacks registered with the given event.
     *
     * @param string $event_name The name of the action to be executed.
     * @param mixed  ...$args    Optional. Additional arguments which are passed on to the
     *                           listeners to the action. Default empty.
     * @return void
     */
    public function action(string $event_name, ...$args);

    /**
     * @psalm-suppress MissingReturnType
     * Executes all the callbacks registered with the given event.
     *
     * @param string $event_name The name of the action to be executed.
     * @param mixed  ...$args    Optional. Additional arguments which are passed on to the
     *                           listeners to the action. Default empty.
     */
    public function dispatch(string $event_name, ...$args);

    /**
     * Filters the given value by applying all the changes from the callbacks
     * registered with the given event. Returns the filtered value.
     *
     * @param string $event_name The name of the event name.
     * @param mixed  $value      The value to filter.
     * @param mixed  ...$args    Additional parameters to pass to the callback functions.
     * @return mixed The filtered value after all listeners are applied to it.
     */
    public function filter(string $event_name, $value, ...$args);

    /**
     * Get the name of the event that WordPress plugin API is executing. Returns
     * false if it isn't executing an event.
     *
     * @return string|bool
     */
    public function currentEventName();

    /**
     * Checks the WordPress plugin API to see if the given event has
     * the given callback. The priority of the callback will be returned
     * or false. If no callback is given will return true or false if
     * there's any callbacks registered to the event.
     *
     * @param string        $event_name               The name of the event name.
     * @param array|callable|false|string $callback Optional. The callback to check for. Default false.
     * @return bool|int If $function_to_check is omitted, returns boolean for whether the event has
     *                   anything registered. When checking a specific function, the priority of that
     *                   event is returned, or false if the function is not attached. When using the
     *                   $function_to_check argument, this function may return a non-boolean value
     *                   that evaluates to false (e.g.) 0, so use the === operator for testing the
     *                   return value.
     */
    public function hasListener(string $event_name, $callback = false);
}
