<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

/**
 * Class ListenerHolderFactory
 * @package ItalyStrap\Event\PsrDispatcher
 */
class ListenerHolderFactory {

	public function makeListenerHolder( callable $listener ): ListenerHolderInterface {
		return new ListenerHolder( $listener );
	}
}
