<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\SubscriberInterface;

class SomeCLass {
	private $state = 0;
	public function doSomething() {
		$this->state++;
		return 'Test returned from: ' . __METHOD__ . ' with value: ' . $this->state;
	}
}

class Subscriber implements SubscriberInterface {

	/**
	 * @var \stdClass
	 */
	private $stdClass;

	/**
	 * Subscriber constructor.
	 * @param \stdClass $stdClass
	 */
	public function __construct( \stdClass $stdClass  ) {
		codecept_debug( 'executed from: ' . __METHOD__ );
		codecept_debug( $stdClass );
		$this->stdClass = $stdClass;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		codecept_debug('executed from: ' . __METHOD__ );
		return [
			'event'	=> 'method',
		];
	}

	public function method(...$args) {
		codecept_debug( $this->stdClass );
		codecept_debug('executed from: ');
		codecept_debug( __METHOD__ );
	}
}

class SubscriberServiceProvider extends Subscriber implements SubscriberInterface {
	/**
	 * @var Subscriber
	 */
	private $subscriber;

	/**
	 *  constructor.
	 * @param Subscriber $subscriber
	 */
	public function __construct( Subscriber $subscriber  ) {
		$this->subscriber = $subscriber;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return parent::getSubscribedEvents();
	}

	public function method( ...$args ) {
		codecept_debug( $args );
		$this->subscriber->method( ...$args );
	}
}

class Listener {

}
