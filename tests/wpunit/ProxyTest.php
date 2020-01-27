<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Auryn\Injector;
use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\SubscriberInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

// phpcs:disable
require_once codecept_data_dir( 'fixtures/classes.php' );
// phpcs:enable

class ProxyTest extends WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	public function setUp(): void {
		// Before...
		parent::setUp();



		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	// tests
	public function testSomeFeature() {

		$injector = new Injector();
		$factory = new LazyLoadingValueHolderFactory();
		$hooks = new Hooks();
		$event_manager = new EventManager( $hooks );

		/** @var string $class_name */
		$class_name = Subscriber::class;

		/** @var Subscriber $subscriber */
		$subscriber = $factory->createProxy(
			$class_name,
			function (
				&$wrappedObject,
				LazyLoadingInterface $subscriber,
				$method,
				$parameters,
				&$initializer
			) use (
				$injector,
				$class_name
			) {
				$wrappedObject = $injector->make( $class_name ); // instantiation logic here
				$initializer   = null; // turning off further lazy initialization
			}
		);

//		$proxy_subscriber = new SubscriberServiceProvider( $subscriber );
		$proxy_subscriber = $injector
			->share(SubscriberServiceProvider::class )
			->make( SubscriberServiceProvider::class, [
			':subscriber'	=> $subscriber,
		] );

		codecept_debug( 'executed from: ' . __METHOD__ );
		$event_manager->addSubscriber( $proxy_subscriber );
		codecept_debug( 'executed from: ' . __METHOD__ );

//		$event_manager->removeSubscriber( $proxy_subscriber );
		$event_manager->removeSubscriber( $injector->make( SubscriberServiceProvider::class ) );
		$hooks->execute( 'event', 'Value from the event' );
		codecept_debug( 'executed from: ' . __METHOD__ );
	}
}
