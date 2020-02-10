<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\Hooks;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/psr-14.php' );
// phpcs:enable
/**
 * Class Psr14IntegrationTest
 * @package ItalyStrap\Tests
 */
class Psr14IntegrationTest extends WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var EventDispatcherInterface
	 */
	private $event_dispatcher;
	/**
	 * @var StoppableEventInterface
	 */
	private $event;
	/**
	 * @var ListenerProviderInterface
	 */
	private $listener;

	/**
	 * @return object
	 */
	public function getEventDispatcher() {
		return $this->event_dispatcher;
	}

	public function setUp(): void {
		// Before...
		parent::setUp();
		$this->event_dispatcher = new HooksDispatcher();

		$this->event = new EventFirst();

		$this->listener = new class implements ListenerProviderInterface {

			/**
			 * @inheritDoc
			 */
			public function getListenersForEvent( object $event ): iterable {
				// TODO: Implement getListenersForEvent() method.
			}
		};
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
	public function event() {
		$sut = $this->getEventDispatcher();

		$sut->addListener( \get_class($this->event), __NAMESPACE__ . '\listener_one' );

		/** @var object $event */
		$event = $sut->dispatch( $this->event );

		$this->assertEquals( 42, $event->value, '' );
//		$this->assertTrue( $event->isPropagationStopped(), '' );
	}
}
