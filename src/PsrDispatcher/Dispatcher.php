<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

use ItalyStrap\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class Dispatcher extends EventDispatcher implements EventDispatcherInterface {

	/**
	 * @var array
	 */
	private $wp_filter;

	/**
	 * @var CallableFactoryInterface
	 */
	private $factory;

	/**
	 * Dispatcher constructor.
	 * @param array $wp_filter
	 * @param CallableFactoryInterface $factory
	 */
	public function __construct( array &$wp_filter, CallableFactoryInterface $factory ) {
		$this->wp_filter = &$wp_filter;
		$this->factory = $factory;
	}

	/**
	 * @inheritDoc
	 */
	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = parent::ORDER,
		int $accepted_args = parent::ARGS
	): bool {
		/** @var callable $callback */
		$callback = $this->factory->buildCallable( $listener );
		return parent::addListener( $event_name, $callback, $priority, $accepted_args );
	}

	/**
	 * @inheritDoc
	 */
	public function removeListener( string $event_name, callable $listener, int $priority = parent::ORDER ): bool {

		if ( ! isset( $this->wp_filter[ $event_name ][ $priority ] ) ) {
			return false;
		}

		foreach ( (array) $this->wp_filter[ $event_name ][ $priority ] as $method_name_registered => $value ) {
			if ( ! $value['function'][0] instanceof ListenerHolderInterface ) {
				throw new \RuntimeException( \sprintf(
					'The callable is not an instance of %s',
					ListenerHolderInterface::class
				) );
			}

			if ( $value['function'][0]->listener() === $listener ) {
				$value['function'][0]->nullListener();
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch( object $event ) {
		$this->execute( \get_class( $event ), $event );
		return $event;
	}
}
