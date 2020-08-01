<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use function add_filter;
use function apply_filters;
use function current_filter;
use function do_action;
use function func_get_args;
use function has_filter;
use function remove_all_filters;
use function remove_filter;

/**
 * Class Hooks
 * @package ItalyStrap\Event
 */
class EventDispatcher implements EventDispatcherInterface {

	protected const ACCEPTED_ARGS = 3;
	protected const PRIORITY = 10;

	/**
	 * @inheritDoc
	 */
	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = self::PRIORITY,
		int $accepted_args = self::ACCEPTED_ARGS
	): bool {
		return add_filter( ...func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function removeListener(
		string $event_name,
		callable $listener,
		int $priority = self::PRIORITY
	): bool {
		return remove_filter( ...func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function removeAllListener( string $event_name, $priority = false ):bool {
		return remove_all_filters( $event_name, $priority );
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 */
	public function execute( string $event_name, ...$args ): void {
		do_action( ...func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch( $event_name, ...$args ) {
		$this->execute( ...func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function filter( string $event_name, $value, ...$args ) {
		return apply_filters( ...func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function currentEventName() {
		return current_filter();
	}

	/**
	 * @inheritDoc
	 */
	public function hasListener( string $event_name, $function_to_check = false ) {
		return has_filter( $event_name, $function_to_check );
	}
}
