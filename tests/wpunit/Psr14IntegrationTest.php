<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

		global $wp_filter, $wp_actions;
		$wp_filter = $wp_actions = [];

		$this->event_dispatcher = new HooksDispatcher();

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

		global $wp_filter, $wp_actions;
		unset($wp_filter, $wp_actions);
		// Then...
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function event() {

		global $wp_filter, $wp_actions;

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation' );
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );

		$sut->removeListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );

//		codecept_debug($wp_filter);
		codecept_debug($wp_filter[EventFirst::class]->callbacks);
//		codecept_debug($wp_actions);

		$event = new EventFirst();

		/** @var object $event */
		$sut->dispatch( $event );
		$sut->dispatch( $event );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );
	}

	/**
	 *
	 */
	public function synfonyEvent() {
		$dispatcher = new EventDispatcher();

		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_to_42' );
		$dispatcher->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
//		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false' );
//		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );
//		$dispatcher->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);

		$event = new EventFirst();

		$event = $dispatcher->dispatch( $event );
		$this->assertEquals( false, $event->value, '' );

//		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_to_42' );
//		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false' );
//		$dispatcher->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );

		$event = $dispatcher->dispatch( $event );
		codecept_debug($event->value);
//		$this->assertEquals( 77, $event->value, '' );

		codecept_debug( $dispatcher->getListeners( EventFirst::class ) );
	}
}
