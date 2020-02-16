<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\ParameterKeys;
use ItalyStrap\Event\SubscriberInterface;
use PhpParser\Node\Arg;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

/**
 * Class SubscriberRegisterTest
 * @package ItalyStrap\Tests
 */
class SubscriberRegisterTest extends Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $hooks;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $subscriber;

	/**
	 * @return EventDispatcher
	 */
	public function getHooks(): EventDispatcher {
		return $this->hooks->reveal();
	}

	/**
	 * @return SubscriberInterface
	 */
	public function getSubscriber(): SubscriberInterface {
		return $this->subscriber->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->hooks = $this->prophesize( EventDispatcher::class );
		$this->subscriber = $this->prophesize( SubscriberInterface::class );
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	private function getInstance() {
		$sut = new SubscriberRegister( $this->getHooks() );
		$this->assertInstanceOf( SubscriberRegister::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldbeInstantiable() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldThrownErrorIfArrayIsEmpty() {
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn([]);

		$this->expectException( \InvalidArgumentException::class );
		$sut->addSubscriber( $this->getSubscriber() );
	}

	public function subscriberProvider() {
		return [
			'event_name => callback'					=> [
				[
					'event_name' 			=> 'callback',
					'event_name1' 			=> 'callback',
				]
			],
			'event_name => [callback|priority]'		=> [
				[
					'event_name' => [
						ParameterKeys::CALLBACK		=> 'callback',
						ParameterKeys::PRIORITY		=> 20,
					],
					'event_name1' => [
						ParameterKeys::CALLBACK		=> 'callback',
						ParameterKeys::PRIORITY		=> 20,
					],
				]
			],
			'event_name => [callback|priority|args]'	=> [
				[
					'event_name' => [
						ParameterKeys::CALLBACK		=> 'callback',
						ParameterKeys::PRIORITY		=> 20,
						ParameterKeys::ACCEPTED_ARGS	=> 6,
					],
					'event_name1' => [
						ParameterKeys::CALLBACK		=> 'callback',
						ParameterKeys::PRIORITY		=> 20,
						ParameterKeys::ACCEPTED_ARGS	=> 6,
					],
				]
			],
			'event_name => [[callback|priority|args]]'	=> [
				[
					'event_name' => [
						[
							ParameterKeys::CALLBACK		=> 'onCallback',
							ParameterKeys::PRIORITY		=> 10,
							ParameterKeys::ACCEPTED_ARGS	=> 6,
						],
						[
							ParameterKeys::CALLBACK		=> 'onCallback',
							ParameterKeys::PRIORITY		=> 20,
							ParameterKeys::ACCEPTED_ARGS	=> 6,
						],
					],
				]
			],
		];
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 */
	public function itShouldAddSubscriberWith( $sub_args ) {
		$test = $this;
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($sub_args);

		$this->hooks->addListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $args ) use ( $sub_args, $test ) {
			$test->assertArgsPassedAreCorrect(  $args, $sub_args  );
			return true;
		})->shouldBeCalled();

		$sut->addSubscriber( $this->getSubscriber() );
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 */
	public function itShouldRemoveSubscriberWith( $sub_args ) {
		$test = $this;
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($sub_args);

		$this->hooks->removeListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $args ) use ( $sub_args, $test ) {
			$test->assertArgsPassedAreCorrect(  $args, $sub_args  );
			return true;
		})->shouldBeCalled();

		$sut->removeSubscriber( $this->getSubscriber() );
	}

	/**
	 * @param $args
	 * @param $sub_args
	 */
	private function assertArgsPassedAreCorrect( $args, $sub_args ): void {

		/**
		 * $args[0] Is the 'event_name'
		 * $args[1] Is the callback
		 * $args[2] Is the priority
		 * $args[3] Is the number of passed arguments
		 */

		$event_name = $args[ 0 ];
		$called_method = $args[ 1 ][ 1 ];
		$priority = $args[ 2 ];
		$accepted_args = $args[ 3 ];

		Assert::assertArrayHasKey( $event_name, $sub_args, 'Both should be the "event_name"' );

		if ( isset( $sub_args[ $event_name ][0] ) && is_array( $sub_args[ $event_name ][0] ) ) {
			foreach ( $sub_args[ $event_name ] as $arg ) {
				$this->assertValueFromArrayAreCorrect(
					[$event_name => $arg],
					$called_method,
					$event_name,
					$arg[ParameterKeys::PRIORITY],
					$arg[ParameterKeys::ACCEPTED_ARGS]
				);
			}
			return;
		}

		$this->assertValueFromArrayAreCorrect( $sub_args, $called_method, $event_name, $priority, $accepted_args );
	}

	private function assertValueFromArrayAreCorrect(
		$args,
		$called_method,
		$event_name,
		$priority,
		$accepted_args
	): void {
		Assert::assertEquals(
			$called_method,
			$args[ $event_name ][ ParameterKeys::CALLBACK ] ?? $args[ $event_name ],
			'Should be callback name'
		);

		Assert::assertEquals(
			$priority,
			$args[ $event_name ][ ParameterKeys::PRIORITY ] ?? 10, // 10 is the default priority
			'Should be default priority'
		);

		Assert::assertEquals(
			$accepted_args,
			$args[ $event_name ][ ParameterKeys::ACCEPTED_ARGS ] ?? 1, // 1 is the defaul number of passed argument
			'Should be default accepted args'
		);
	}

	/**
	 * @test
	 */
	public function itShouldThrownIfParameterOfSubscriberIsNotValid() {
		$test = $this;
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn([
			'event_name' 			=> [new \stdClass()],
		]);

		$this->hooks->addListener()->shouldNotBeCalled();

		$this->expectException( \RuntimeException::class );
		$sut->addSubscriber( $this->getSubscriber() );
	}
}
