<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventResolverExtension;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberInterface;
use WpunitTester;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/classes.php' );
// phpcs:enable
/**
 * Class IntegrationTest
 * @package ItalyStrap\Tests
 */
class IntegrationTest extends WPTestCase {

	/**
	 * @var WpunitTester
	 */
	protected $tester;

	/**
	 * @var EventDispatcher
	 */
	private $dispatcher;

	/**
	 * @var SubscriberRegister
	 */
	private $register;

	public function setUp(): void {
		// Before...
		parent::setUp();

		$_SERVER['REQUEST_TIME'] = \time();

		global $wp_filter;
		$wp_filter = [];

		$this->dispatcher = new EventDispatcher();
		$this->register = new SubscriberRegister( $this->dispatcher );

		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		global $wp_filter;
		$wp_filter = [];
		// Then...
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function itShouldOutputTextOnEventName() {

		$this->dispatcher->addListener( 'event_name', function () {
			echo 'Value printed';
		} );

		$this->expectOutputString( 'Value printed' );
		$this->dispatcher->dispatch( 'event_name' );
	}

	/**
	 * @test
	 */
	public function testClassWithDispatchDependency() {
		$some_class = new ClassWithDispatchDependency( $this->dispatcher );

		$this->dispatcher->addListener(
			ClassWithDispatchDependency::EVENT_NAME,
			function ( string $value ) {
				return 'New value';
			}
		);

		$some_class->filterValue();

		$this->assertStringContainsString('New value', $some_class->value(), '');
	}

	/**
	 * @test
	 */
	public function subscriberShouldEchoTextWhenEventIsExecuted() {

		$subscriber = new class implements SubscriberInterface {

			/**
			 * @inheritDoc
			 */
			public function getSubscribedEvents(): array {
				return [
					'event_name'	=> 'method',
					'other_event_name'	=> [
						[
							SubscriberInterface::CALLBACK		=> 'onCallback',
							SubscriberInterface::PRIORITY		=> 20,
							SubscriberInterface::ACCEPTED_ARGS	=> 6,
						],
						[
							SubscriberInterface::CALLBACK		=> 'onCallback',
							SubscriberInterface::PRIORITY		=> 10,
							SubscriberInterface::ACCEPTED_ARGS	=> 6,
						],
					],
				];
			}

			public function method() {
				echo 'Value printed';
			}

			public function onCallback( string $filtered ) {
				return $filtered . ' Value printed';
			}
		};

		$this->register->addSubscriber( $subscriber );

		$this->expectOutputString( 'Value printed' );
		$this->dispatcher->dispatch( 'event_name' );

		$filtered = (string) $this->dispatcher->filter( 'other_event_name', '' );
		$this->assertStringContainsString( 'Value printed Value printed', $filtered, '' );
	}

	/**
	 * @test
	 */
	public function itShouldPrintText() {

		$injector = new Injector();
		$injector->share($injector);

		$injector->alias(EventDispatcherInterface::class, EventDispatcher::class);
		$injector->share( EventDispatcherInterface::class );
		$injector->share( SubscriberRegister::class );
		$event_resolver = $injector->make( EventResolverExtension::class, [
			':config'	=> ConfigFactory::make([
				Subscriber::class	=> false
			]),
		] );

		$dependencies = ConfigFactory::make([
//			AurynResolver::ALIASES	=> [
//				HooksInterface::class	=> Hooks::class,
//			],
//			AurynResolver::SHARING	=> [
//				HooksInterface::class,
//				EventManager::class,
//			],
			EventResolverExtension::SUBSCRIBERS	=> [
				Subscriber::class,
//				Subscriber::class	=> false,
			],
		]);

//		$empress = new AurynResolver( $injector, $dependencies );
		$empress = $injector->make( AurynResolver::class, [
			':dependencies'	=> $dependencies
		] );
		$empress->extend( $event_resolver );
		$empress->resolve();

		$this->expectOutputString( 'Some text' );
		( $injector->make( EventDispatcher::class ) )->dispatch( 'event' );
	}

	private function configExample() {

		$test = [
			'hook_name => callback'					=> [
				[
					'hook_name' 			=> 'callback'
				]
			],
			'hook_name => [callback|priority]'		=> [
				[
					'hook_name' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
					]
				]
			],
			'hook_name => [callback|priority|args]'	=> [
				[
					'hook_name' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
						SubscriberInterface::ACCEPTED_ARGS	=> 6,
					]
				]
			],
		];

		$config = [
			'subscribers'	=> [
				Subscriber::class,
			],
			'listeners'	=> [
				Listener::class 	=> [
					'event_name'	=> '',
					'method'	=> '',
					'priority'	=> '',
					'args'	=> '',
				]
			],
		];
	}
}
