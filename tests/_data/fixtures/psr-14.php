<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Psr\EventDispatcher\StoppableEventInterface;

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
