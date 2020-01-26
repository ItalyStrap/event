<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\Hooks;
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

		$this->hooks = new Hooks();
		$this->manager = new EventManager( $this->hooks );

		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	// Tests
	public function testItWorks() {

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

		$this->manager->add( $subscriber );

		$this->expectOutputString( 'Ciao' );
		$this->hooks->execute( 'event_name', 'value passed', 'other', 2, 3 );
	}

	public function testSomeThing() {
		$this->hooks->addListener( 'test', function () {
			codecept_debug( __METHOD__ );
		}, 10, 1 );

		$this->hooks->execute( 'test' );
	}

	private function configExample() {

		$config = [
			'subscribers'	=> [
				Subscriber::class,
			],
			'listeners'	=> [
				Listener::class
			],
		];
	}
}
