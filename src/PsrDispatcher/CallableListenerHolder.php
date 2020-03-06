<?php
declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class ListenerHolder
 * @package ItalyStrap\Event\PsrDispatcher
 */
class CallableListenerHolder implements ListenerHolderInterface {

	/**
	 * @var callable
	 */
	private $listener;

	/**
	 * ListenerHolder constructor.
	 * @param callable $listener
	 */
	public function __construct( callable $listener ) {
		$this->listener = $listener;
	}

	/**
	 * @inheritDoc
	 */
	public function listener(): callable {
		return $this->listener;
	}

	/**
	 * @inheritDoc
	 */
	public function nullListener(): void {
		$this->listener = function ( object $event ): void {
		};
	}

	/**
	 * The method called from the WordPress Plugin API on event
	 * This method MUST check if the $event is stoppable or not and
	 * then call the $listener and pass the $event object in it
	 *
	 * @param object $event
	 * @return void
	 */
	public function __invoke( object $event ) {

		if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
			return;
		}

		$listener = $this->listener;
		$listener( $event );
	}
}
