<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Interface Keys
 * @package ItalyStrap\Event
 * @deprecated
 */
final class ParameterKeys implements SubscriberInterface {
//	const CALLBACK = 'function_to_add';
//	const PRIORITY = 'priority';
//	const ACCEPTED_ARGS = 'accepted_args';

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return [];
	}
}
