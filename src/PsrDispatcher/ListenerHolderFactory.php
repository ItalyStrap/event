<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

/**
 * Class ListenerHolderFactory
 * @package ItalyStrap\Event\PsrDispatcher
 */
class ListenerHolderFactory {

	/**
	 * @param callable $listener
	 * @return callable
	 */
	public function buildListenerHolderCallable( callable $listener ): callable {
		return [new ListenerHolder( $listener ), 'execute'];
	}
}
