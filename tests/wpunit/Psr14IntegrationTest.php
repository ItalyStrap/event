<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
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
	 * @return EventDispatcherInterface
	 */
	public function getEvent(): EventDispatcherInterface {
		return $this->event_dispatcher;
	}

	public function setUp(): void {
		// Before...
		parent::setUp();
		$this->event_dispatcher = new class implements  EventDispatcherInterface {

			/**
			 * @inheritDoc
			 */
			public function dispatch( object $event ) {
				// TODO: Implement dispatch() method.
			}
		};

		$this->event = new class implements StoppableEventInterface {

			/**
			 * @inheritDoc
			 */
			public function isPropagationStopped(): bool {
				// TODO: Implement isPropagationStopped() method.
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
		$sut = $this->getEvent();

		$sut->dispatch( new class {
		} );
	}
}
