<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

function listener_change_value_to_42( object $event ): void {
	$event->value = 42;
}

function listener_change_value_to_false_and_stop_propagation( object $event ): void {
	$event->value = false;
	\method_exists($event, 'stopPropagation') and $event->stopPropagation();
}

function listener_change_value_to_77( object $event ): void {
	$event->value = 77;
}

function get_text() {
	return 'new value';
}

function on_callback(...$args) {

}
