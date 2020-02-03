<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Empress\Injector;
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

	/**
	 * @test
	 */
	public function testProxyFactory() {

		$injector = new Injector();
//		$injector->share( $injector );

		$hooks = new Hooks();
		$event_manager = new EventManager( $hooks );

		/** @var string $class_name */
		$class_name = Subscriber::class;
		$service_provider = SubscriberServiceProvider::class;
		/** @var string $args_name */
		$args_name = ':subscriber'; // Bind to $class_name proxied


		codecept_debug( 'executed from: ' . __METHOD__ );

		codecept_debug( 'Delegate' );

		$factory = new SubscriberFactory( $injector, new LazyLoadingValueHolderFactory() );
//		$subscriber_dep = $factory->make();

//		$subscriber_dep->check = 1;

//		$injector->share( $subscriber_dep );

//		codecept_debug( get_class($subscriber_dep) );

//		$injector->delegate( Subscriber::class, [ $factory, 'make' ] );

		codecept_debug( 'Called before make' );
		/** @var \ItalyStrap\Event\SubscriberInterface $subscriber */
		$subscriber = $injector->make( SubscriberServiceProvider::class );
		$event_manager->addSubscriber( $subscriber );
		$hooks->execute( 'event', 'Value from the event' );

		$obj = $subscriber->getSubscriberObj();

		$injector->share(Subscriber::class);
//		codecept_debug( 'Inspect' );
//		codecept_debug( $injector->inspect( Subscriber::class ) );
//		codecept_debug( $injector->inspect() );

//		$this->assertEquals( $subscriber_dep->check, $obj->check, '' );
	}

	/**
	 */
	public function proxyFactoryWip() {

		$injector = new Injector();

//		$injector->share( $injector );

		$factory = new LazyLoadingValueHolderFactory();
		$hooks = new Hooks();
		$event_manager = new EventManager( $hooks );

		/** @var string $class_name */
		$class_name = Subscriber::class;
		$service_provider = SubscriberServiceProvider::class;
		/** @var string $args_name */
		$args_name = ':subscriber'; // Bind to $class_name proxied


		/** @var Subscriber $proxy_subscriber */
//		$proxy_subscriber = $factory->createProxy(
//			$class_name,
//			function (
//				&$wrappedObject,
//				LazyLoadingInterface $proxy_subscriber,
//				$method,
//				$parameters,
//				&$initializer
//			) use (
//				$injector,
//				$class_name
//			) {
//				$wrappedObject = $injector->make( $class_name ); // instantiation logic here
//				$initializer   = null; // turning off further lazy initialization
//			}
//		);

//		$service_subscriber = $injector
//			->share( $service_provider )
//			->make( $service_provider
//				, [
//					$args_name	=> $proxy_subscriber,
//				]
//			);

		codecept_debug( 'executed from: ' . __METHOD__ );
//		$event_manager->addSubscriber( $service_subscriber );
		codecept_debug( 'executed from: ' . __METHOD__ );

//		$event_manager->removeSubscriber( $service_subscriber );
//		$event_manager->removeSubscriber( $injector->make( SubscriberServiceProvider::class ) );
//		$hooks->execute( 'event', 'Value from the event' );
//		codecept_debug( 'executed from: ' . __METHOD__ );

//
//		$service_provider_callable = $injector->make( SubscriberServiceProviderCallable::class
//			, [
//			$args_name	=> function () use ( $injector ) {
//				return $injector->make( Subscriber::class );
//			},
//		]
//		);
//		$event_manager->addSubscriber( $service_provider_callable );
//		$hooks->execute( 'event', 'Value from the event' );
//		codecept_debug( 'executed from: ' . __METHOD__ );

		codecept_debug( 'Delegate' );
		$injector->delegate( Subscriber::class, [SubscriberFactory::class, 'make'] );

		/** @var SubscriberFactory $factory */
//		$factory = $injector->make( SubscriberFactory::class );
		codecept_debug( 'Called before make' );
//		$subscriber = $injector->make( Subscriber::class );
//		codecept_debug( $subscriber->getSubscribedEvents() );
//		codecept_debug( $subscriber->method() );

		/** @var \ItalyStrap\Event\SubscriberInterface $subscriber */
		$subscriber = $injector->make( SubscriberServiceProvider::class );
//		$subscriber->getSubscribedEvents();
		$event_manager->addSubscriber( $subscriber );
//		codecept_debug( $subscriber->method() );
		$hooks->execute( 'event', 'Value from the event' );


//		$subscriber = $injector->make( Subscriber::class );
//		$subscriber->getSubscribedEvents();
	}
}
