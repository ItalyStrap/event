<?php
declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

/**
 * Class ListenerHolderFactory
 * @package ItalyStrap\Event\PsrDispatcher
 */
interface CallableFactoryInterface {

	/**
	 * @param callable $listener
	 * @return callable
	 */
	public function buildCallable( callable $listener ): callable;
}
