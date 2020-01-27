<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\Keys;
use ItalyStrap\Event\SubscriberInterface;

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

	// Tests
	private function testItWorks() {

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
//				codecept_debug( \func_num_args() );
//				codecept_debug( \func_get_args() );
				echo 'Ciao';
			}
		};

		$this->manager->addSubscriber( $subscriber );

		$this->expectOutputString( 'Ciao' );
		$this->hooks->execute( 'event_name', 'value passed', 'other', 2, 3 );
	}

	private function testSomeThing() {
		$this->hooks->addListener( 'test', function () {
			codecept_debug( __METHOD__ );
		}, 10, 1 );

		$this->hooks->execute( 'test' );

		global $wp_filter;

//		foreach ( $wp_filter['test'][10] as $key => $value ) {
//			codecept_debug( $key );
//			codecept_debug( $value );
//		}

		codecept_debug( 'Implements:' );
		codecept_debug( \class_implements( Subscriber::class ) );
	}

	private function useCases() {

		$this->hooks->filter( 'event_name', '' );
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
