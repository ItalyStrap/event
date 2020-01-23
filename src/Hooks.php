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
}
