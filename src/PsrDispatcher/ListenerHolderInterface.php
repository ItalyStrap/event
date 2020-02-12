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

	/**
	 * The method called from the WordPress Plugin API on event
	 * This method MUST check if the $event is stoppable or not and
	 * then call the $listener and pass the $event object in it
	 *
	 * @param object $event
	 * @return void
	 */
	public function execute( object $event );
}
