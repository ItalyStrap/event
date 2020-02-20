<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Fig\EventDispatcher\ParameterDeriverTrait;
use ItalyStrap\Event\PsrDispatcher\ListenerHolder;
use ItalyStrap\Event\PsrDispatcher\ListenerHolderInterface;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\StoppableEventInterface;
use stdClass;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/psr-14.php' );
// phpcs:enable

class ListenerHolderTest extends \Codeception\Test\Unit {

	use ParameterDeriverTrait;

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance( callable  $listener ) {
		$sut = new ListenerHolder( $listener );
		$this->assertInstanceOf( ListenerHolderInterface::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance( static function ( object $event ) {
		} );
	}

	/**
	 * @test
	 */
	public function itShouldReturnListener() {
		$listener = static function ( object $event ) {
		};
		$sut = $this->getInstance( $listener );
		$this->assertSame( $listener, $sut->listener(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnVoidListener() {
		$event = new \stdClass();
		$event->value = 0;

		$listener = static function ( object $event ) {
			$event->value = 42;
		};
		$sut = $this->getInstance( $listener );
		$sut->nullListener();
		$sut->execute( $event );

		$this->assertEmpty( $event->value, '' );
	}

	/**
	 * @test
	 */
	public function itShouldExecute() {
		$event = $this->prophesize( StoppableEventInterface::class );

		$event->isPropagationStopped()->willReturn( false );

		$calls = 0;
		$listener = static function ( object $event_obj ) use ( $event, &$calls ) {
			Assert::assertSame( $event->reveal(), $event_obj, '' );
			$calls++;
		};

		$sut = $this->getInstance( $listener );
		$sut->execute( $event->reveal() );

		$this->assertTrue( 1 === $calls, '' );
	}

	/**
	 * @test
	 */
	public function itShouldNotExecuteIfEventIsStopped() {
		$event = $this->prophesize( StoppableEventInterface::class );

		$event->isPropagationStopped()->willReturn( true );

		$calls = 0;
		$listener = static function ( object $event_obj ) use ( $event, &$calls ) {
			// Never called
			$calls++;
		};

		$sut = $this->getInstance( $listener );
		$sut->execute( $event->reveal() );

		$this->assertTrue( 0 === $calls, '' );
	}

	/**
	 * @test
	 */
	public function testSomeCallable() {
		$event = new stdClass;

		$listener = new ListenerChangeValueToText();
		$sut = $this->getInstance( [ $listener, 'changeText' ] );

		$sut->execute( $event );

		$this->assertTrue( 'new value' === $event->value, '' );
	}

	/**
	 * @test
	 */
//    public function testSomeFeature()
//    {
//		$sut = $this->getInstance( static function ( object $event ) {} );
//		codecept_debug( $type = $this->getParameterType( function ( object $event ) {} ) );
//		$this->assertTrue( $type === 'object' || ( new \ReflectionClass( $type ) )->isInstantiable() );
//    }
}