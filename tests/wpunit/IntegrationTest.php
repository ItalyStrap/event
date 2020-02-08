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
					'event_name'	=> 'method'
				];
			}

			public function method() {
				echo 'Value printed';
			}
		};

		$this->manager->addSubscriber( $subscriber );

		$this->expectOutputString( 'Value printed' );
		$this->hooks->execute( 'event_name' );
	}

	/**
	 * @test
	 */
	public function itShouldPrintText() {
		$injector = new Injector();
		$dependencies = ConfigFactory::make([
			EventResolverExtension::KEY	=> [
				Subscriber::class,
			],
		]);
		$empress = new AurynResolver( $injector, $dependencies );

		$event_resolver = $injector->make( EventResolverExtension::class, [
			':config'	=> ConfigFactory::make([]),
		] );

		$empress->extend( $event_resolver );

		$empress->resolve();
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
