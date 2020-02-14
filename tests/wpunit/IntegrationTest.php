<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\EventResolverExtension;
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\HooksInterface;
use ItalyStrap\Event\Keys;
use ItalyStrap\Event\SubscriberInterface;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/classes.php' );
// phpcs:enable
/**
 * Class IntegrationTest
 * @package ItalyStrap\Tests
 */
class IntegrationTest extends WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var Hooks
	 */
	private $hooks;

	/**
	 * @var EventManager
	 */
	private $manager;

	public function setUp(): void {
		// Before...
		parent::setUp();

		global $wp_filter;
		$wp_filter = [];

		$this->hooks = new Hooks();
		$this->manager = new EventManager( $this->hooks );

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

		$this->hooks->addListener( 'event_name', function () {
			echo 'Value printed';
		}, 10, 1 );

		$this->expectOutputString( 'Value printed' );
		$this->hooks->execute( 'event_name' );
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
							Keys::CALLBACK		=> 'onCallback',
							Keys::PRIORITY		=> 20,
							Keys::ACCEPTED_ARGS	=> 6,
						],
						[
							Keys::CALLBACK		=> 'onCallback',
							Keys::PRIORITY		=> 10,
							Keys::ACCEPTED_ARGS	=> 6,
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

		$this->manager->addSubscriber( $subscriber );

		$this->expectOutputString( 'Value printed' );
		$this->hooks->execute( 'event_name' );

		$filtered = (string) $this->hooks->filter( 'other_event_name', '' );
		$this->assertStringContainsString( 'Value printed Value printed', $filtered, '' );
	}

	/**
	 * @test
	 */
	public function itShouldPrintText() {

		$injector = new Injector();
		$injector->share($injector);

		$injector->alias(HooksInterface::class, Hooks::class);
		$injector->share( HooksInterface::class );
		$injector->share( EventManager::class );
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
			EventResolverExtension::KEY	=> [
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
		( $injector->make( Hooks::class ) )->execute( 'event' );
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
						Keys::CALLBACK		=> 'callback',
						Keys::PRIORITY		=> 20,
					]
				]
			],
			'hook_name => [callback|priority|args]'	=> [
				[
					'hook_name' => [
						Keys::CALLBACK		=> 'callback',
						Keys::PRIORITY		=> 20,
						Keys::ACCEPTED_ARGS	=> 6,
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
