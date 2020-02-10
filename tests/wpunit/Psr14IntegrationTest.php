<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\Hooks;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

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
		$this->event_dispatcher = new class extends Hooks implements EventDispatcherInterface {

			/**
			 * @inheritDoc
			 */
			public function dispatch( object $event ) {

				if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
					\remove_all_filters( \get_class( $event ) );
					return $event;
				}

				$this->execute( \get_class( $event ), $event );
				return $event;
			}
		};

		$this->event = new class implements StoppableEventInterface {

			public $value = 0;

			private $propagation = false;

			public function stopPropagation(): void {
				$this->propagation = true;
			}

			/**
			 * @inheritDoc
			 */
			public function isPropagationStopped(): bool {
				return $this->propagation;
			}
		};

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

		$sut->addListener( \get_class($this->event), function ( $event ) {
			$event->value = 42;
			$event->stopPropagation();
		});

		$event = $sut->dispatch( $this->event );

		$this->assertEquals( 42, $event->value, '' );
		$this->assertTrue( $this->event->isPropagationStopped(), '' );
	}
}
