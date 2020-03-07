<?php
declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

use ItalyStrap\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface as PsrDispatcherInterface;

class PsrDispatcher implements PsrDispatcherInterface {


	/**
	 * @var array
	 */
	private $wp_filter;

	/**
	 * @var CallableFactoryInterface
	 */
	private $factory;

	/**
	 * @var EventDispatcher
	 */
	private $dispatcher;

	/**
	 * Dispatcher constructor.
	 * @param array $wp_filter
	 * @param CallableFactoryInterface $factory
	 * @param EventDispatcher $dispatcher
	 */
	public function __construct(
		array &$wp_filter,
		CallableFactoryInterface $factory,
		EventDispatcher $dispatcher
	) {
		$this->wp_filter = &$wp_filter;
		$this->factory = $factory;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @inheritDoc
	 */
	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = 10,
		int $accepted_args = 1
	): bool {
		/** @var callable $callback */
		$callback = $this->factory->buildCallable( $listener );
		return $this->dispatcher->addListener( $event_name, $callback, $priority, $accepted_args );
	}

	/**
	 * @inheritDoc
	 */
	public function removeListener(
		string $event_name,
		callable $listener,
		int $priority = 10
	): bool {

		if ( ! isset( $this->wp_filter[ $event_name ][ $priority ] ) ) {
			return false;
		}

		foreach ( (array) $this->wp_filter[ $event_name ][ $priority ] as $method_name_registered => $value ) {
			if ( ! $value['function'] instanceof ListenerHolderInterface ) {
				throw new \RuntimeException( \sprintf(
					'The callable is not an instance of %s',
					ListenerHolderInterface::class
				) );
			}

			if ( $value['function']->listener() === $listener ) {
				$value['function']->nullListener();
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch( object $event ) {
		$this->dispatcher->dispatch( \get_class( $event ), $event );
		return $event;
	}
}
