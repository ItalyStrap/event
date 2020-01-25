<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Event\EvenManager;
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\Keys;
use ItalyStrap\Event\SubscriberInterface;
use PhpParser\Node\Arg;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

/**
 * Class EventManagerTest
 * @package ItalyStrap\Tests
 */
class EventManagerTest extends Unit {

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
	 * @return Hooks
	 */
	public function getHooks(): Hooks {
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
		$this->hooks = $this->prophesize( Hooks::class );
		$this->subscriber = $this->prophesize( SubscriberInterface::class );
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	private function getInstance() {
		$sut = new EvenManager( $this->getHooks() );
		$this->assertInstanceOf( EvenManager::class, $sut, '' );
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
		$sut->add( $this->getSubscriber() );
	}

	public function subscriberProvider() {
		return [
			'hook_name => callback'					=> [
				[
					'hook_name' 			=> 'callback'
				]
			],
			'hook_name => [callback|priority]'		=> [
				[
					'hook_name' => [
						Keys::CALLBACK		=> 'callback',
						Keys::PRIORITY		=> 20,
					]
				]
			],
			'hook_name => [callback|priority|args]'	=> [
				[
					'hook_name' => [
						Keys::CALLBACK		=> 'callback',
						Keys::PRIORITY		=> 20,
						Keys::ACCEPTED_ARGS	=> 6,
					]
				]
			],
		];
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 */
	public function itShouldAddSubscriber( $sub_args ) {
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($sub_args);

		$this->hooks->addListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $args ) use ($sub_args) {

			Assert::assertEquals( $args[0], \array_keys( $sub_args )[0], 'Both should are "hook_name"' );

			Assert::assertEquals(
				$args[1][1],
				$sub_args['hook_name'][Keys::CALLBACK] ?? $sub_args['hook_name'],
				'Should be callback name'
			);

			Assert::assertEquals(
				$args[2],
				$sub_args['hook_name'][Keys::PRIORITY] ?? 10,
				'Should be default priority'
			);

			Assert::assertEquals(
				$args[3],
				$sub_args['hook_name'][Keys::ACCEPTED_ARGS] ?? 1,
				'Should be default accepted args'
			);
		})->shouldBeCalled();

		$sut->add( $this->getSubscriber() );
	}

	/**
	 * @test
	 * @dataProvider subscriberProvider()
	 */
	public function itShouldRemoveSubscriber( $sub_args ) {
		$sut = $this->getInstance();

		$this->subscriber->getSubscribedEvents()->willReturn($sub_args);

		$this->hooks->removeListener(
			Argument::type( 'string' ),
			Argument::type( 'callable' ),
			Argument::type( 'int' ),
			Argument::type( 'int' )
		)->will(function ( $args ) use ($sub_args) {

			Assert::assertEquals( $args[0], \array_keys( $sub_args )[0], 'Both should are "hook_name"' );

			Assert::assertEquals(
				$args[1][1],
				$sub_args['hook_name'][Keys::CALLBACK] ?? $sub_args['hook_name'],
				'Should be callback name'
			);

			Assert::assertEquals(
				$args[2],
				$sub_args['hook_name'][Keys::PRIORITY] ?? 10,
				'Should be default priority'
			);

			Assert::assertEquals(
				$args[3],
				$sub_args['hook_name'][Keys::ACCEPTED_ARGS] ?? 1,
				'Should be default accepted args'
			);
		})->shouldBeCalled();

		$sut->remove( $this->getSubscriber() );
	}
}
