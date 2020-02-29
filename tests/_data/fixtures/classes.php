<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberInterface;

class SomeCLass {
	private $state = 0;
	public function doSomething() {
		$this->state++;
		return 'Test returned from: ' . __METHOD__ . ' with value: ' . $this->state;
	}
}

class Subscriber implements SubscriberInterface {

	public $check = 0;

	/**
	 * @var \stdClass
	 */
	private $stdClass;

	/**
	 * Subscriber constructor.
	 * @param \stdClass $stdClass
	 */
	public function __construct( \stdClass $stdClass  ) {
		$this->stdClass = $stdClass;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return [
			'event'	=> 'method',
		];
	}

	public function method() {
		echo 'Some text';
	}
}

class SubscriberServiceProvider extends Subscriber implements SubscriberInterface {
	/**
	 * @var Subscriber
	 */
	private $subscriber;

	public function getSubscriberObj(): Subscriber {
		return $this->subscriber;
	}

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

	public function method() {
		$this->subscriber->method();
	}
}

class SubscriberServiceProviderCallable extends Subscriber implements SubscriberInterface {
	/**
	 * @var Subscriber
	 */
	private $subscriber;

	/**
	 *  constructor.
	 * @param Subscriber $subscriber
	 */
	public function __construct( callable $subscriber  ) {
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
		$subscriber = $this->subscriber;
		$this->subscriber = $subscriber();
		$this->subscriber->method( ...$args );
	}
}

class Listener {

}

class ClassWithDispatchDependency {

	const EVENT_NAME = 'event_name';

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	private $value = '';

	/**
	 * ClassWithDispatchDependency constructor.
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct( EventDispatcherInterface $dispatcher ) {
		$this->dispatcher = $dispatcher;
	}

	public function filterValue() {
		$this->value = $this->dispatcher->filter(
			static::EVENT_NAME,
			$this->value
		);
	}

	public function value() {
		return $this->value;
	}
}
