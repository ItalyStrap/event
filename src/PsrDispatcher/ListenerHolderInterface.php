<?php
declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

/**
 * Interface ListenerHolderInterface
 * @package ItalyStrap\Event\PsrDispatcher
 */
interface ListenerHolderInterface {

	/**
	 * This is the listener callable
	 * @return callable
	 */
	public function listener(): callable;

	/**
	 * Use this to change the listener to an empty callable.
	 */
	public function nullListener(): void;
}
