<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\PsrDispatcher\Dispatcher;
use ItalyStrap\Event\PsrDispatcher\CallableFactory;
use ItalyStrap\Event\PsrDispatcher\ListenerHolderInterface;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;
use tad\FunctionMockerLe;

/**
 * Class DispatcherTest
 * @package ItalyStrap\Tests
 */
class DispatcherTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $factory;

	/**
	 * @return \ItalyStrap\Event\PsrDispatcher\CallableFactoryInterface
	 */
	public function getFactory(): \ItalyStrap\Event\PsrDispatcher\CallableFactoryInterface {
		return $this->factory->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		FunctionMockerLe\undefineAll([
			'do_action',
			'add_filter',
			'remove_filter',
			'apply_filters',
			'current_filter',
			'has_filter',
			'remove_all_filters'
		]);

		$this->factory = $this->prophesize( CallableFactory::class );
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
		$sut = new Dispatcher( $wp_filter, $this->getFactory() );
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
		$event = new \stdClass();
		$expected = [
			'event_name'	=> get_class( $event ),
			'event'			=> $event,
		];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define( 'do_action', function ( $event_name, $event ) use ( $expected ) {
			$this->assertSame( $expected['event_name'], $event_name );
			$this->assertSame( $expected['event'], $event );
		} );

		$sut = $this->getInstance();
		$sut->dispatch( $event );
	}

	/**
	 * @test
	 */
	public function itShouldAddListener() {
		$eventObj = new stdClass();
		$eventName = get_class( $eventObj );

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define(
			'add_filter',
			function ( string $event_name, object $event ) use ( $eventName ) {
				$this->assertSame( $eventName, $event_name, '' );
				return true;
			}
		);

		$this->factory
			->buildCallable( Argument::type('callable'))
			->will(function ($args) {
				return function () {
				};
			});

		$sut = $this->getInstance();
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
		$listener_holder->listener()->willReturn($listener);
		$listener_holder->nullListener()->shouldBeCalled();
		$listener_holder->execute()->shouldNotBeCalled();

		$wp_filter[$eventName][10][\uniqid()]['function'] = [
			$listener_holder->reveal(),
			'execute'
		];

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

		$wp_filter[$eventName][10][\uniqid()]['function'] = [
			$listener_holder->reveal(),
			'execute'
		];

		$sut = $this->getInstance();

		$this->expectException( \RuntimeException::class );
		$sut->removeListener( $eventName, $listener );
	}
}
