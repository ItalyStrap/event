<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use ItalyStrap\Event\SubscriberInterface as Subscriber;
use RuntimeException;
use function gettype;
use function is_iterable;
use function is_string;
use function sprintf;

class SubscriberRegister implements SubscriberRegisterInterface {


	private const ACCEPTED_ARGS = 1;
	private const PRIORITY = 10;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	/**
	 * EvenManager constructor.
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct( EventDispatcherInterface $dispatcher ) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @inheritDoc
	 */
	public function addSubscriber( Subscriber $subscriber ): void {
		foreach ( $subscriber->getSubscribedEvents() as $event_name => $parameters ) {
			if ( isset( $parameters[0] ) && is_iterable( $parameters[0] ) ) {
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
		$this->dispatcher->addListener(
			$event_name,
			$this->buildCallable( $subscriber, $parameters ),
			...$this->buildParameters( $parameters )
		);
	}

	/**
	 * @inheritDoc
	 */
	public function removeSubscriber( Subscriber $subscriber ): void {
		foreach ( $subscriber->getSubscribedEvents() as $event_name => $parameters ) {
			if ( isset( $parameters[0] ) && is_iterable( $parameters[0] ) ) {
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
		$this->dispatcher->removeListener(
			$event_name,
			$this->buildCallable( $subscriber, $parameters ),
			...$this->buildParameters( $parameters )
		);
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
		} elseif ( isset( $parameters[ Subscriber::CALLBACK ] ) ) {
			/** @var callable $callable */
			$callable = [$subscriber, $parameters[ Subscriber::CALLBACK ]];
		} else {
			throw new RuntimeException( sprintf(
				'Impossible to build a valid callable because $parameters is a type %s',
				gettype( $parameters )
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
			$parameters[ Subscriber::PRIORITY ] ?? self::PRIORITY,
			$parameters[ Subscriber::ACCEPTED_ARGS ] ?? self::ACCEPTED_ARGS,
		];
	}
}
