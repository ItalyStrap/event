<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Hooks;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class HooksDispatcher extends Hooks implements EventDispatcherInterface {

	/**
	 * @inheritDoc
	 */
	public function dispatch( object $event ) {

		if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
			$this->removeAllListener( \get_class( $event ) );
			return $event;
		}

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

function listener_one( object $event ) {
	$event->value = 42;
//	$event->stopPropagation();
}

function listener_two( object $event ) {
	$event->value = false;
//	$event->stopPropagation();
}

function listener_three( object $event ) {
	$event->value = 77;
//	$event->stopPropagation();
}
