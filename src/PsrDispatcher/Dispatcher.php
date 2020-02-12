<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

use ItalyStrap\Event\Hooks;
use Psr\EventDispatcher\EventDispatcherInterface;

class Dispatcher extends Hooks implements EventDispatcherInterface {

	/**
	 * @var array
	 */
	private $wp_filter;

	/**
	 * @var ListenerHolderFactory
	 */
	private $factory;

	/**
	 * Dispatcher constructor.
	 * @param array $wp_filter
	 * @param ListenerHolderFactory $factory
	 */
	public function __construct( array &$wp_filter, ListenerHolderFactory $factory ) {
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
	) {
		/** @var callable $callback */
		$callback = [ $this->factory->makeListenerHolder( $listener ), 'execute'];
		parent::addListener( $event_name, $callback, $priority, $accepted_args );
	}

	/**
	 * @inheritDoc
	 */
	public function removeListener( string $event_name, callable $listener, int $priority = parent::ORDER ) {

		if ( ! isset( $this->wp_filter[ $event_name ][ $priority ] ) ) {
			return;
		}

		foreach ( (array) $this->wp_filter[ $event_name ][ $priority ] as $method_name_registered => $value ) {
			if ( $value['function'][0]->listener() === $listener ) {
				$value['function'][0]->nullListener();
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch( object $event ) {
		$this->execute( \get_class( $event ), $event );
		return $event;
	}
}
