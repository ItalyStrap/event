<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\Hooks;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

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
//		$factory = new LazyLoadingValueHolderFactory();
//
//		/** @var SomeCLass $proxy */
//		$proxy = $factory->createProxy(
//			SomeCLass::class,
//			function (& $wrappedObject, $proxy, $method, $parameters, & $initializer) {
//				$wrappedObject = new SomeCLass(); // instantiation logic here
//				$initializer   = null; // turning off further lazy initialization
//			}
//		);
//
//		codecept_debug( $proxy->doSomething() );

		$factory = new LazyLoadingValueHolderFactory();
		/** @var Subscriber $subscriber */
		$subscriber = $factory->createProxy(
			Subscriber::class,
			function (&$wrappedObject, $subscriber, $method, $parameters, &$initializer) {
				$wrappedObject = new Subscriber(); // instantiation logic here
				$initializer   = null; // turning off further lazy initialization
				codecept_debug( $subscriber );
				codecept_debug( $method );
				codecept_debug( $parameters );
			}
		);

		$hooks = new Hooks();
		$event_manager = new EventManager( $hooks );
		$event_manager->addSubscriber( $subscriber );

		$hooks->execute( 'event' );
	}
}
