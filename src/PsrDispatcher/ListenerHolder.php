<?php
declare(strict_types=1);

namespace ItalyStrap\Event\PsrDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class ListenerHolder
 * @package ItalyStrap\Event\PsrDispatcher
 */
class ListenerHolder implements ListenerHolderInterface {

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
		$this->listener = function ( object $event ) {
		};
	}

	/**
	 * @inheritDoc
	 */
	public function execute( object $event ) {

		if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
			return;
		}

		$listener = $this->listener;
		$listener( $event );
	}
}
