<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\SubscriberInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use UnitTester;

/**
 * Class SubscriberRegisterTest
 * @package ItalyStrap\Tests
 */
class SubscriberRegisterTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	private $hooks;

	/**
	 * @var ObjectProphecy
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
	public function itShouldThrownErrorIfArrayIsEmptygggg() {
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->will(function (){

			yield 'event_name' 			=> 'callback';

			yield 'event_name' => [
				SubscriberInterface::CALLBACK		=> 'callback',
				SubscriberInterface::PRIORITY		=> 20,
			];

			yield 'event_name1' => [
				SubscriberInterface::CALLBACK		=> 'callback',
				SubscriberInterface::PRIORITY		=> 20,
			];

			yield 'event_name' => [
				[
					SubscriberInterface::CALLBACK		=> 'onCallback',
					SubscriberInterface::PRIORITY		=> 10,
					SubscriberInterface::ACCEPTED_ARGS	=> 6,
				],
				[
					SubscriberInterface::CALLBACK		=> 'onCallback',
					SubscriberInterface::PRIORITY		=> 20,
					SubscriberInterface::ACCEPTED_ARGS	=> 6,
				],
			];

			return [
				'event_name' 			=> 'callback',
				'event_name1' 			=> 'callback',
			];
		});

		$this->hooks->addListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $listener_args )
//		use ( $provider_args, $test )
		{
//			$test->assertArgsPassedAreCorrect(  $listener_args, $provider_args  );
			return true;
		})->shouldBeCalled();

		$sut->addSubscriber( $this->getSubscriber() );
//		$sut->removeSubscriber( $this->getSubscriber() );
	}

	public function subscriberProvider() {
		return [
			/**
			 * @TODO Potrebbe essere utile chiamare direttamente
			 *       una callback
			 */
//			'event_name => callable'					=> [
//				[
//					'event_name' 			=> function () {},
////					'event_name1' 			=> [ new \stdClass(), 'run' ],
//				]
//			],
			'event_name => callback'					=> [
				[
					'event_name' 			=> 'callback',
					'event_name1' 			=> 'callback',
				]
			],
			'event_name => [callback|priority]'		=> [
				[
					'event_name' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
					],
					'event_name1' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
					],
				]
			],
			'event_name => [callback|priority|args]'	=> [
				[
					'event_name' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
						SubscriberInterface::ACCEPTED_ARGS	=> 6,
					],
					'event_name1' => [
						SubscriberInterface::CALLBACK		=> 'callback',
						SubscriberInterface::PRIORITY		=> 20,
						SubscriberInterface::ACCEPTED_ARGS	=> 6,
					],
				]
			],
			'event_name => [[callback|priority|args]]'	=> [
				[
					'event_name' => [
						[
							SubscriberInterface::CALLBACK		=> 'onCallback',
							SubscriberInterface::PRIORITY		=> 10,
							SubscriberInterface::ACCEPTED_ARGS	=> 6,
						],
						[
							SubscriberInterface::CALLBACK		=> 'onCallback',
							SubscriberInterface::PRIORITY		=> 20,
							SubscriberInterface::ACCEPTED_ARGS	=> 6,
						],
					],
				]
			],
		];
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 * @param $provider_args
	 */
	public function itShouldAddSubscriberWith( $provider_args ) {
		$test = $this;
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($provider_args);

		$this->hooks->addListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $listener_args ) use ( $provider_args, $test ) {
			$test->assertArgsPassedAreCorrect(  $listener_args, $provider_args  );
			return true;
		})->shouldBeCalled();

		$sut->addSubscriber( $this->getSubscriber() );
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 */
	public function itShouldRemoveSubscriberWith( $provider_args ) {
		$test = $this;
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($provider_args);

		$this->hooks->removeListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $listener_args ) use ( $provider_args, $test ) {
			$test->assertArgsPassedAreCorrect(  $listener_args, $provider_args  );
			return true;
		})->shouldBeCalled();

		$sut->removeSubscriber( $this->getSubscriber() );
	}

	/**
	 * @param $listener_args
	 * @param $provider_args
	 */
	private function assertArgsPassedAreCorrect( $listener_args, $provider_args ): void {

		/**
		 * $args[0] Is the 'event_name'
		 * $args[1] Is the callback
		 * $args[2] Is the priority
		 * $args[3] Is the number of passed arguments
		 */

		$event_name = $listener_args[ 0 ];
//		$called_method = \is_callable( $listener_args[ 1 ] ) ? $listener_args[ 1 ] : $listener_args[ 1 ][ 1 ];
		$called_method = $listener_args[ 1 ][ 1 ];
		$priority = $listener_args[ 2 ];
		$accepted_args = $listener_args[ 3 ];

		Assert::assertArrayHasKey( $event_name, $provider_args, 'Both should be the "event_name"' );

		if ( isset( $provider_args[ $event_name ][0] ) && is_array( $provider_args[ $event_name ][0] ) ) {
			foreach ($provider_args[ $event_name ] as $arg ) {
				$this->assertValueFromArrayAreCorrect(
					[$event_name => $arg],
					$called_method,
					$event_name,
					$arg[SubscriberInterface::PRIORITY],
					$arg[SubscriberInterface::ACCEPTED_ARGS]
				);
			}
			return;
		}

		$this->assertValueFromArrayAreCorrect( $provider_args, $called_method, $event_name, $priority, $accepted_args );
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
			$args[ $event_name ][ SubscriberInterface::CALLBACK ] ?? $args[ $event_name ],
			'Should be callback name'
		);

		Assert::assertEquals(
			$priority,
			$args[ $event_name ][ SubscriberInterface::PRIORITY ] ?? 10, // 10 is the default priority
			'Should be default priority'
		);

		Assert::assertEquals(
			$accepted_args,
			$args[ $event_name ][ SubscriberInterface::ACCEPTED_ARGS ] ?? 1, // 1 is the defaul number of passed argument
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
