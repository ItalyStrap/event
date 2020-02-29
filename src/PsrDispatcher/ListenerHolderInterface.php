<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

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
	 * Use this to set the listener to a null callable
	 */
	public function nullListener(): void;
}
