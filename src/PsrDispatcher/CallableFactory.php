<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

class CallableFactory implements CallableFactoryInterface {

	/**
	 * @inheritDoc
	 */
	public function buildCallable( callable $listener ): callable {
		return [new ListenerHolder( $listener ), 'execute'];
	}
}
