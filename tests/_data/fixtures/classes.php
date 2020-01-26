<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\SubscriberInterface;

class SomeCLass {
	public function doSomething() {
		return 'Test';
	}
}

class Subscriber implements SubscriberInterface {

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		codecept_debug('executed');
		return [
			'event'	=> 'method',
		];
	}

	public function method() {
		codecept_debug( __METHOD__ );
	}
}

class Listener {

}
