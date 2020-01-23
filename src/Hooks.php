<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Class Hooks
 * @package ItalyStrap\Event
 */
class Hooks implements HooksInterface {

	private const ARGS = 3;
	private const ORDER = 10;

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
		int $priority = self::ORDER,
		int $accepted_args = self::ARGS
	) {
		\add_filter( ...\func_get_args() );
	}

	/**
	 * Removes the given event listener from the list of event listeners
	 * that listen to the given event.
	 *
	 * @param string   $event_name
	 * @param callable $listener
	 * @param int      $priority
	 */
	public function removeListener( string $event_name, callable $listener, int $priority = self::ORDER ) {
		\remove_filter( ...\func_get_args() );
	}

	/**
	 * Executes all the callbacks registered with the given hook.
	 *
	 * @param string $tag    The name of the action to be executed.
	 * @param mixed  ...$arg Optional. Additional arguments which are passed on to the
	 *                       functions hooked to the action. Default empty.
	 * @return void
	 */
	public function execute( string $tag, ...$arg ) {
		\do_action( ...\func_get_args() );
	}

	/**
	 * Filters the given value by applying all the changes from the callbacks
	 * registered with the given hook. Returns the filtered value.
	 *
	 * @param string $tag     The name of the filter hook.
	 * @param mixed  $value   The value to filter.
	 * @param mixed  ...$args Additional parameters to pass to the callback functions.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function filter( string $tag, $value, ...$args ) {
		return \apply_filters( ...\func_get_args() );
	}
}
