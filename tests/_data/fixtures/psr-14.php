<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Hooks;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventHolder {

	private $listener;

	public function __construct( callable $listener ) {
		$this->listener = $listener;
	}

	public function execute( object $event ) {

		if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
			return;
		}

		$listener = $this->listener;
		$listener( $event );
	}
}

class HooksDispatcher extends Hooks implements EventDispatcherInterface {

	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = self::ORDER,
		int $accepted_args = self::ARGS
	) {
		$callback = [ new EventHolder( $listener ), 'execute'];
		parent::addListener( $event_name, $callback, $priority, $accepted_args );
	}

//	public function removeListener( string $event_name, callable $listener, int $priority = self::ORDER ) {
//		global $wp_filter;
//
//		if ( ! isset( $wp_filter[ $event_name ][ $priority ] ) ) {
//			return;
//		}
//
//		foreach ( (array) $wp_filter[ $event_name ][ $priority ] as $method_name_regstered => $value ) {
//
//			codecept_debug( $method_name_regstered );
//			codecept_debug( $value );
//		}
//	}

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

function listener_change_value_to_42( object $event ) {
	$event->value = 42;
//	$event->stopPropagation();
}

function listener_change_value_to_false_and_stop_propagation( object $event ) {
	$event->value = false;
	$event->stopPropagation();
}

function listener_change_value_to_77( object $event ) {
	$event->value = 77;
//	$event->stopPropagation();
}

class ListenerChangeValueToText {
	public function changeText( object $event ) {
		$event->value = get_text();
	}
}

function get_text() {
	return 'new value';
}
