<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Interface HooksInterface
 * @package ItalyStrap\Event
 */
interface HooksInterface {

	/**
	 * Adds the given event listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param string   $event_name
	 * @param callable $listener
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	public function addListener(
		string $event_name,
		callable $listener,
		int $priority,
		int $accepted_args
	);

	/**
	 * Removes the given event listener from the list of event listeners
	 * that listen to the given event.
	 *
	 * @param string   $event_name
	 * @param callable $listener
	 * @param int      $priority
	 */
	public function removeListener( string $event_name, callable $listener, int $priority );

	/**
	 * Remove all of the listener from an event.
	 *
	 * @param string $event_name
	 * @param bool $priority
	 * @return true
	 */
	public function removeAllListener( string $event_name, $priority = false );

	/**
	 * Executes all the callbacks registered with the given hook.
	 *
	 * @param string $event_name The name of the action to be executed.
	 * @param mixed  ...$args    Optional. Additional arguments which are passed on to the
	 *                           functions hooked to the action. Default empty.
	 * @return void
	 */
	public function execute( string $event_name, ...$args );

	/**
	 * Filters the given value by applying all the changes from the callbacks
	 * registered with the given hook. Returns the filtered value.
	 *
	 * @param string $event_name The name of the filter hook.
	 * @param mixed  $value      The value to filter.
	 * @param mixed  ...$args    Additional parameters to pass to the callback functions.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function filter( string $event_name, $value, ...$args );

	/**
	 * Get the name of the event that WordPress plugin API is executing. Returns
	 * false if it isn't executing a hook.
	 *
	 * @return string|bool
	 */
	public function currentEventName();

	/**
	 * Checks the WordPress plugin API to see if the given hook has
	 * the given callback. The priority of the callback will be returned
	 * or false. If no callback is given will return true or false if
	 * there's any callbacks registered to the hook.
	 *
	 * @param string        $event_name               The name of the filter hook.
	 * @param callable|bool $function_to_check Optional. The callback to check for. Default false.
	 * @return false|int If $function_to_check is omitted, returns boolean for whether the hook has
	 *                   anything registered. When checking a specific function, the priority of that
	 *                   hook is returned, or false if the function is not attached. When using the
	 *                   $function_to_check argument, this function may return a non-boolean value
	 *                   that evaluates to false (e.g.) 0, so use the === operator for testing the
	 *                   return value.
	 */
	public function hasListener( string $event_name, $function_to_check = false );
}
