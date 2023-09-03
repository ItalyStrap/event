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
}
