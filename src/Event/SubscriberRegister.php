<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use ItalyStrap\Event\SubscriberInterface as Subscriber;
use InvalidArgumentException;
use function get_class;
use function is_array;
use function is_string;
use function sprintf;

class SubscriberRegister implements SubscriberRegisterInterface {


	private const ARGS = 1;
	private const ORDER = 10;

	/**
	 * @var EventDispatcherInterface
	 */
	private $hooks;

	/**
	 * EvenManager constructor.
	 * @param EventDispatcherInterface $hooks
	 */
	public function __construct( EventDispatcherInterface $hooks ) {
		$this->hooks = $hooks;
	}

	/**
	 * @inheritDoc
	 */
	public function addSubscriber( Subscriber $subscriber ): void {
		$map = $this->assertSubscriberIsNotEmpty( $subscriber );
		foreach ( $map as $event_name => $parameters ) {
			if ( isset( $parameters[0] ) && is_array( $parameters[0] ) ) {
				foreach ( $parameters as $listener ) {
					$this->addSubscriberListener( $subscriber, $event_name, $listener );
				}
				continue;
			}

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
		$this->hooks->addListener(
			$event_name,
			$this->buildCallable( $subscriber, $parameters ),
			...$this->buildParameters( $parameters )
		);
	}

	/**
	 * @inheritDoc
	 */
	public function removeSubscriber( Subscriber $subscriber ): void {
		$map = $this->assertSubscriberIsNotEmpty( $subscriber );
		foreach ( $map as $event_name => $parameters ) {
			if ( isset( $parameters[0] ) && is_array( $parameters[0] ) ) {
				foreach ( $parameters as $listener ) {
					$this->removeSubscriberListener( $subscriber, $event_name, $listener );
				}
				continue;
			}
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
		$this->hooks->removeListener(
			$event_name,
			$this->buildCallable( $subscriber, $parameters ),
			...$this->buildParameters( $parameters )
		);
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
			/** @var callable $callable */
			$callable = [$subscriber, $parameters];
		} elseif ( isset( $parameters[ ParameterKeys::CALLBACK ] ) ) {
			/** @var callable $callable */
			$callable = [$subscriber, $parameters[ ParameterKeys::CALLBACK ]];
		} else {
			throw new \RuntimeException(\sprintf(
				'Impossible to build a valid callable because $parameters is a type %s',
				\gettype( $parameters )
			));
		}

		return $callable;
	}

	/**
	 * @param mixed $parameters
	 * @return array
	 */
	private function buildParameters( $parameters ): array {
		return [
			$parameters[ ParameterKeys::PRIORITY ] ?? self::ORDER,
			$parameters[ ParameterKeys::ACCEPTED_ARGS ] ?? self::ARGS,
		];
	}
}