<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Hooks;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

interface ListenerHolderInterface {
	public function listener(): callable;
	public function nullListener(): void ;
	public function execute( object $event );
}

class ListenerHolder implements ListenerHolderInterface {

	private $listener;

	/**
	 * @return callable
	 */
	public function listener(): callable {
		return $this->listener;
	}

	public function __construct( callable $listener ) {
		$this->listener = $listener;
	}

	public function nullListener(): void {
		$this->listener = function ( object $event ) {};
	}

	public function execute( object $event ) {

		if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
			return;
		}

		$listener = $this->listener;
		$listener( $event );
	}
}

class ListenerHolderFactory {
	public function makeListenerHolder( callable $listener ): ListenerHolderInterface {
		return new ListenerHolder( $listener );
	}
}

class HooksDispatcher extends Hooks implements EventDispatcherInterface {

	/**
	 * @var array
	 */
	private $wp_filter;

	/**
	 * @var ListenerHolderFactory
	 */
	private $factory;

	public function __construct( array &$wp_filter, ListenerHolderFactory $factory ) {
		$this->wp_filter = &$wp_filter;
		$this->factory = $factory;
	}

	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = parent::ORDER,
		int $accepted_args = parent::ARGS
	) {
		$callback = [ $this->factory->makeListenerHolder( $listener ), 'execute'];
		parent::addListener( $event_name, $callback, $priority, $accepted_args );
	}

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

class EventFirst implements StoppableEventInterface {

	public $value = 0;

	protected $propagationStopped = false;

	public function stopPropagation(): void {
		$this->propagationStopped = true;
	}

	/**
	 * @inheritDoc
	 */
	public function isPropagationStopped(): bool {
		return $this->propagationStopped;
	}
};

function listener_change_value_to_42( object $event ): void {
	$event->value = 42;
}

function listener_change_value_to_false_and_stop_propagation( object $event ): void {
	$event->value = false;
	$event->stopPropagation();
}

function listener_change_value_to_77( object $event ): void {
	$event->value = 77;
}

class ListenerChangeValueToText {
	public function changeText( object $event ): void {
		$event->value = get_text();
	}
}

function get_text() {
	return 'new value';
}
