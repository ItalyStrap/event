<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\PsrDispatcher\Dispatcher;
use ItalyStrap\Event\PsrDispatcher\CallableFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use WpunitTester;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/psr-14.php' );
// phpcs:enable
/**
 * Class Psr14IntegrationTest
 * @package ItalyStrap\Tests
 */
class Psr14IntegrationTest extends WPTestCase {

	/**
	 * @var WpunitTester
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
		global $wp_filter;
		return new Dispatcher( $wp_filter, new CallableFactory() );
	}

	public function setUp(): void {
		// Before...
		parent::setUp();

		$_SERVER['REQUEST_TIME'] = \time();

		global $wp_filter, $wp_actions;
		$wp_filter = $wp_actions = [];

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
	public function itShouldAddFunctionListenerAndChangeValue() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );

		/** @var object $event */
		$event = $sut->dispatch( new EventFirst() );

		$this->assertEquals( 42, $event->value, '' );
		$this->assertFalse( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldRemoveFunctionListenerAndReturnValueWithoutChanges() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->removeListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );

		/** @var object $event */
		$event = $sut->dispatch( new EventFirst() );

		$this->assertEquals( 0, $event->value, '' );
		$this->assertFalse( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldStopPropagation() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);

		// Here it will set value to false and stop propagation
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation' );
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );


		$event = new EventFirst();

		/** @var object $event */
		$sut->dispatch( $event );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldNotStopPropagation() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );


		$event = new EventFirst();

		/** @var object $event */
		$sut->dispatch( $event );

		$this->assertEquals( 77, $event->value, '' );
		$this->assertFalse( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldRemoveListenerAndReturnValue77() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation' );
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );

		$sut->removeListener(
			EventFirst::class,
			__NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation'
		);

		$event = new EventFirst();

		/** @var object $event */
		$sut->dispatch( $event );

		$this->assertEquals( 77, $event->value, '' );
		$this->assertFalse( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function ifSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagation() {

		$sut = $this->getEventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation' );
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );

		$event = new EventFirst();

		/** @var object $event */
		$event = $sut->dispatch( $event );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );

		$event = $sut->dispatch( new EventFirst() );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );
	}

	/**
	 * @test
	 */
	public function ifSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagationSymfonyMirror() {
		$sut = new EventDispatcher();

		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_42' );
		$sut->addListener( EventFirst::class, [new ListenerChangeValueToText, 'changeText' ]);
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_false_and_stop_propagation' );
		$sut->addListener( EventFirst::class, __NAMESPACE__ . '\listener_change_value_to_77' );

		$event = new EventFirst();

		/** @var object $event */
		$event = $sut->dispatch( $event );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );

		$event = $sut->dispatch( new EventFirst() );

		$this->assertEquals( false, $event->value, '' );
		$this->assertTrue( $event->isPropagationStopped(), '' );
	}

	public function testServerRequest() {
		codecept_debug($_SERVER['REQUEST_TIME']);
		codecept_debug( \json_encode( \is_int( $_SERVER['REQUEST_TIME'] ) ) );
	}
}
