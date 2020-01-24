<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use ItalyStrap\Event\SubscriberInterface as Subscriber;
use InvalidArgumentException;
use function get_class;
use function is_array;
use function is_string;
use function sprintf;

/**
 * Class EvenManager
 * @package ItalyStrap\Event
 */
class EvenManager {

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
	 * @param Subscriber $subscriber
	 */
	public function add( Subscriber $subscriber ): void {
		$map = $this->assertSubscriberIsNotEmpty( $subscriber );
		foreach ( $map as $event_name => $parameters ) {
			$this->addSubscriberListener( $subscriber, $event_name, $parameters );
		}
	}

	/**
	 * Adds the given subscriber listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param Subscriber $subscriber
	 * @param string               $event_name
	 * @param string|array         $parameters
	 */
	private function addSubscriberListener( Subscriber $subscriber, string $event_name, $parameters ): void {
		$callable = $this->buildCallable( $subscriber, $parameters );
		$this->hooks->addListener( $event_name, $callable, ...$this->buildParameters( $parameters ) );
	}

	/**
	 * Removes an event subscriber.
	 *
	 * The event manager removes the given subscriber from the list of event listeners
	 * for all the events that it wants to listen to.
	 *
	 * @param Subscriber $subscriber
	 */
	public function remove( Subscriber $subscriber ): void {
		$map = $this->assertSubscriberIsNotEmpty( $subscriber );
		foreach ( $map as $event_name => $parameters ) {
			$this->removeSubscriberListener( $subscriber, $event_name, $parameters );
		}
	}

	/**
	 * Adds the given subscriber listener to the list of event listeners
	 * that listen to the given event.
	 *
	 * @param Subscriber $subscriber
	 * @param string               $event_name
	 * @param string|array         $parameters
	 */
	private function removeSubscriberListener( Subscriber $subscriber, string $event_name, $parameters ): void {
		$callable = $this->buildCallable( $subscriber, $parameters );
		$this->hooks->removeListener( $event_name, $callable, ...$this->buildParameters( $parameters ) );
	}

	/**
	 * @param Subscriber $subscriber
	 * @return array
	 */
	private function assertSubscriberIsNotEmpty( Subscriber $subscriber ): array {
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
	 * @param Subscriber $subscriber
	 * @param string|array $parameters
	 * @return callable
	 */
	private function buildCallable( Subscriber $subscriber, $parameters ): callable {
		$callable = null;
		if ( is_string( $parameters ) ) {
			$callable = [$subscriber, $parameters];
		} elseif ( is_array( $parameters ) && isset( $parameters[ Keys::CALLBACK ] ) ) {
			$callable = [$subscriber, $parameters[ Keys::CALLBACK ]];
		}
		return $callable;
	}

	/**
	 * @param mixed $parameters
	 * @return array
	 */
	private function buildParameters( $parameters ): array {
		return [
			$parameters[ Keys::PRIORITY ] ?? 10,
			$parameters[ Keys::ACCEPTED_ARGS ] ?? 1,
		];
	}
}