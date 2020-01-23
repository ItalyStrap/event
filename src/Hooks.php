<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Class Hooks
 * @package ItalyStrap\Event
 */
class Hooks implements HooksInterface
{
	const CALLBACK = 'function_to_add';
	const PRIORITY = 'priority';
	const ACCEPTED_ARGS = 'accepted_args';

	/**
	 * Adds the given event listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param string   $event_name
	 * @param callable $listener
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	public function addListener( string $event_name, callable $listener, int $priority = 10, int $accepted_args = 1 ) {
		\add_filter( ...\func_get_args() );
	}
}
