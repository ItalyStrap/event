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

			public function addListener(
				string $event_name,
				callable $listener,
				int $priority = parent::ORDER,
				int $accepted_args = parent::ARGS
			) {
				codecept_debug($event_name);
				parent::addListener( $event_name, $listener, $priority, $accepted_args );
			}

			/**
			 * @inheritDoc
			 */
			public function dispatch( object $event ) {

				if ( $event instanceof StoppableEventInterface && $event->isPropagationStopped() ) {
					return $event;
				}

				$this->execute( \get_class( $event ), $event );
				return $event;
			}
		};

		$this->event = new class implements StoppableEventInterface {

			/**
			 * @inheritDoc
			 */
			public function isPropagationStopped(): bool {
				return false;
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

		codecept_debug(\get_class($this->event));
		codecept_debug(\get_class($this->event));

		$sut->addListener( \get_class($this->event), function ( $event ) {
			codecept_debug( $event );
		});

		$sut->dispatch( $this->event );
	}
}
