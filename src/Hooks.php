<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Class Hooks
 * @package ItalyStrap\Event
 */
class Hooks implements HooksInterface {

	protected const ARGS = 3;
	protected const ORDER = 10;

	/**
	 * @inheritDoc
	 */
	public function addListener(
		string $event_name,
		callable $listener,
		int $priority = self::ORDER,
		int $accepted_args = self::ARGS
	): bool {
		return \add_filter( ...\func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function removeListener(
		string $event_name,
		callable $listener,
		int $priority = self::ORDER
	): bool {
		return \remove_filter( ...\func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function removeAllListener( string $event_name, $priority = false ):bool {
		return \remove_all_filters( $event_name, $priority );
	}

	/**
	 * @inheritDoc
	 */
	public function execute( string $event_name, ...$args ): void {
		\do_action( ...\func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function filter( string $event_name, $value, ...$args ) {
		return \apply_filters( ...\func_get_args() );
	}

	/**
	 * @inheritDoc
	 */
	public function currentEventName() {
		return \current_filter();
	}

	/**
	 * @inheritDoc
	 */
	public function hasListener( string $event_name, $function_to_check = false ) {
		return \has_filter( $event_name, $function_to_check );
	}
}
