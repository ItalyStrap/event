<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Auryn\Injector;
use ItalyStrap\Event\SubscriberInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

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

class SubscriberFactory {
	/**
	 * @var Injector
	 */
	private $injector;
	/**
	 * @var LazyLoadingValueHolderFactory
	 */
	private $proxy;

	/**
	 * SubscriberFactory constructor.
	 * @param Injector $injector
	 * @param LazyLoadingValueHolderFactory $proxy
	 */
	public function __construct( Injector $injector, LazyLoadingValueHolderFactory $proxy ) {
		$this->injector = $injector;
		$this->proxy = $proxy;
	}

	/**
	 * @return object
	 */
	public function make(): object {

		$proxy_subscriber = $this->proxy->createProxy(
			Subscriber::class,
			function (
				&$wrappedObject,
				LazyLoadingInterface $proxy_subscriber,
				$method,
				$parameters,
				&$initializer
			) {
				$wrappedObject = $this->injector->make( Subscriber::class ); // instantiation logic here
				$initializer   = null; // turning off further lazy initialization
			}
		);

		codecept_debug( 'Called from' . __METHOD__ );

		return $proxy_subscriber;
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

	public function method( ...$args ) {
		codecept_debug( $args );
		$this->subscriber->method( ...$args );
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
