<?php

namespace ItalyStrap\Event;

use ItalyStrap\Event\SubscriberInterface as Subscriber;

/**
 * Class SubscriberRegister
 * @package ItalyStrap\Event
 */
interface SubscriberRegisterInterface {

	/**
	 * Adds an event subscriber.
	 *
	 * The event manager adds the given subscriber to the list of event listeners
	 * for all the events that it wants to listen to.
	 *
	 * @param Subscriber $subscriber
	 */
	public function addSubscriber( Subscriber $subscriber ): void;

	/**
	 * Removes an event subscriber.
	 *
	 * The event manager removes the given subscriber from the list of event listeners
	 * for all the events that it wants to listen to.
	 *
	 * @param Subscriber $subscriber
	 */
	public function removeSubscriber( Subscriber $subscriber ): void;
}
