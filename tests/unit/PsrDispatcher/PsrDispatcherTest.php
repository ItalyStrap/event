<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\PsrDispatcher\CallableFactoryInterface;
use ItalyStrap\PsrDispatcher\PsrDispatcher;
use ItalyStrap\PsrDispatcher\CallableFactory;
use ItalyStrap\PsrDispatcher\ListenerHolderInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use stdClass;
use UnitTester;
use function uniqid;

/**
 * Class DispatcherTest
 * @package ItalyStrap\Tests
 */
class PsrDispatcherTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;
	/**
	 * @var ObjectProphecy
	 */
	private $factory;

	/**
	 * @var ObjectProphecy
	 */
	private $dispatcher;

	/**
	 * @return EventDispatcher
	 */
	public function getDispatcher(): EventDispatcher {
		return $this->dispatcher->reveal();
	}

	/**
	 * @return CallableFactoryInterface
	 */
	public function getFactory(): CallableFactoryInterface {
		return $this->factory->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->factory = $this->prophesize( CallableFactory::class );
		$this->dispatcher = $this->prophesize( EventDispatcher::class );
		global $wp_filter;
		$wp_filter = [];
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
		global $wp_filter;
		$wp_filter = [];
	}

	public function getInstance() {
		global $wp_filter;
		$sut = new PsrDispatcher( $wp_filter, $this->getFactory(), $this->getDispatcher() );
		$this->assertInstanceOf( EventDispatcherInterface::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldDispatch() {
		$event = new stdClass();
		$expected = [
			'event_name'	=> get_class( $event ),
			'event'			=> $event,
		];

		$this->dispatcher
			->dispatch(
				Argument::type('string'),
				Argument::type('object')
			)
			->will(function ( $args ) use ( $expected ) {
				Assert::assertSame( $expected['event_name'], $args[0] );
				Assert::assertSame( $expected['event'], $args[1] );
			})
			->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->dispatch( $event );
	}

	/**
	 * @test
	 */
	public function itShouldAddListener() {
		$eventObj = new stdClass();
		$eventName = get_class( $eventObj );

		$sut = $this->getInstance();

		$this->factory
			->buildCallable(Argument::type('callable'))
			->will(function ($args) use ($eventObj) {
				return static function () use ($eventObj) {
					return $eventObj;
				};
			})
			->shouldBeCalled();

		$this->dispatcher
			->addListener(
				Argument::type('string'),
				Argument::type('callable'),
				Argument::type('integer'),
				Argument::type('integer')
			)
			->will(function ($args) use ( $eventName ): bool {
				Assert::assertSame( $eventName, $args[0], '' );
				return true;
			})
			->shouldBeCalled();

		$sut->addListener( $eventName, static function (object $event) {
			//No called here
		} );
	}

	/**
	 * @test
	 */
	public function itShouldRemoveListener() {

		global $wp_filter;
		$eventObj = new stdClass();
		$eventName = get_class( $eventObj );

		$listener = static function (object $event) {
			//No called here
		};

		$listener_holder = $this->prophesize( ListenerHolderInterface::class );
		$listener_holder->listener()->willReturn($listener)->shouldBeCalled();
		$listener_holder->nullListener()->shouldBeCalled();

		$wp_filter[$eventName][10][ uniqid()]['function'] = $listener_holder->reveal();

		$sut = $this->getInstance();

		$sut->removeListener( $eventName, $listener );
	}

	/**
	 * @test
	 */
	public function itShouldReturnBeforeRemoveListener() {

		global $wp_filter;
		$eventObj = new stdClass();
		$eventName = get_class( $eventObj );

		$listener = static function (object $event) {
			//No called here
		};

		$listener_holder = $this->prophesize( ListenerHolderInterface::class );
		$listener_holder->listener()->shouldNotBeCalled();

		$wp_filter[$eventName][10] = null;

		$sut = $this->getInstance();

		$this->assertFalse( $sut->removeListener( $eventName, $listener ), '' );
	}

	/**
	 * @test
	 */
	public function itShouldThrownErrorOnRemoveListenerIfIsNotListenerHolderInterface() {

		global $wp_filter;
		$eventObj = new stdClass();
		$eventName = get_class( $eventObj );

		$listener = static function (object $event) {
			//No called here
		};

		$listener_holder = $this->prophesize( stdClass::class );

		$wp_filter[$eventName][10][ uniqid()]['function'] = [
			$listener_holder->reveal(),
			'execute'
		];

		$sut = $this->getInstance();

		$this->expectException( RuntimeException::class );
		$sut->removeListener( $eventName, $listener );
	}
}
