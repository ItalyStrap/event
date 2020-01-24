<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use InvalidArgumentException;
use function get_class;
use function is_array;
use function is_string;
use function sprintf;

/**
 * Class EvenManager
 * @package ItalyStrap\Event
 */
class EvenManager
{
	/**
	 * @var Hooks
	 */
	private $hooks;

	/**
	 * EvenManager constructor.
	 * @param Hooks $hooks
	 */
	public function __construct( Hooks $hooks ) {
		$this->hooks = $hooks;
	}

	/**
	 * Adds an event subscriber.
	 *
	 * The event manager adds the given subscriber to the list of event listeners
	 * for all the events that it wants to listen to.
	 *
	 * @param SubscriberInterface $subscriber
	 */
	public function add( SubscriberInterface $subscriber ) {
		$map = $this->assertSubscriberIsNotEmpty( $subscriber );
		foreach ( $map as $event_name => $parameters ) {
			$this->addSubscriberListener( $subscriber, $event_name, $parameters );
		}
	}

	/**
	 * Adds the given subscriber listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param SubscriberInterface $subscriber
	 * @param string               $event_name
	 * @param string|array         $parameters
	 */
	private function addSubscriberListener( SubscriberInterface $subscriber, string $event_name, $parameters ): void {

		$callable = $this->buildCallable( $subscriber, $parameters );

		$args = [
			$parameters[ Keys::PRIORITY ] ?? 10,
			$parameters[ Keys::ACCEPTED_ARGS ] ?? 1,
		];

		$this->hooks->addListener( $event_name, $callable, ...$args );
	}

	/**
	 * Adds the given subscriber listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param SubscriberInterface $subscriber
	 * @param string               $event_name
	 * @param string|array         $parameters
	 */
	private function removeSubscriberListener( SubscriberInterface $subscriber, string $event_name, $parameters ): void {

		$callable = $this->buildCallable( $subscriber, $parameters );

		$args = [
			$parameters[ Keys::PRIORITY ] ?? 10,
			$parameters[ Keys::ACCEPTED_ARGS ] ?? 1,
		];

		$this->hooks->removeListener( $event_name, $callable, ...$args );
	}

	/**
	 * Removes an event subscriber.
	 *
	 * The event manager removes the given subscriber from the list of event listeners
	 * for all the events that it wants to listen to.
	 *
	 * @param SubscriberInterface $subscriber
	 */
	public function remove( SubscriberInterface $subscriber ) {
		foreach ( $subscriber->getSubscribedEvents() as $event_name => $parameters ) {
			$this->removeSubscriberListener( $subscriber, $event_name, $parameters );
		}
	}

	/**
	 * @param SubscriberInterface $subscriber
	 * @return array
	 */
	private function assertSubscriberIsNotEmpty( SubscriberInterface $subscriber ): array {
		if ( empty( $map = $subscriber->getSubscribedEvents() ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'You have to add at least an event name and a method to %s::class',
					get_class( $subscriber )
				)
			);
		}

		return $map;
	}

	/**
	 * @param SubscriberInterface $subscriber
	 * @param string|array $parameters
	 * @return array
	 */
	private function buildCallable( SubscriberInterface $subscriber, $parameters ): callable {
		$callable = null;
		if ( is_string( $parameters ) ) {
			$callable = [$subscriber, $parameters];
		} elseif ( is_array( $parameters ) && isset( $parameters[ Keys::CALLBACK ] ) ) {
			$callable = [$subscriber, $parameters[ Keys::CALLBACK ]];
		}
		return $callable;
	}
}
