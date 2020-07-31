<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Interface SubscriberInterface
 * @package ItalyStrap\Event
 */
interface SubscriberInterface {

	const CALLBACK		= 'function_to_add';
	const PRIORITY		= 'priority';
	const ACCEPTED_ARGS	= 'accepted_args';

	/**
	 * Returns an array of hooks that this subscriber wants to register with
	 * the WordPress plugin API.
	 *
	 * The array key is the name of the hook. The value can be:
	 *
	 *  * The method name
	 *  * An array with the method name and priority
	 *  * An array with the method name, priority and number of accepted arguments
	 *
	 * For instance:
	 *
	 *  * ['event_name' => 'method_name']
	 *  * [
	 * 		'event_name' =>
	 * 		[
	 * 			KEYS::CALLBACK	=> 'method_name',
	 * 			KEYS::PRIORITY	=> $priority,
	 * 		]
	 * 	  ]
	 *  * [
	 * 		'event_name' =>
	 * 		[
	 * 			KEYS::CALLBACK		=> 'method_name',
	 * 			KEYS::PRIORITY		=> $priority,
	 * 			KEYS::ACCEPTED_ARGS	=> $accepted_args,
	 * 		]
	 * 	  ]
	 *
	 * @return iterable
	 */
	public function getSubscribedEvents(): iterable;
}
