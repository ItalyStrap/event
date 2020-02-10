<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\HooksInterface;
use PHPUnit\Framework\Assert;
use tad\FunctionMockerLe;

/**
 * Class HooksTest
 * @package ItalyStrap\Tests
 */
class HooksTest extends \Codeception\Test\Unit {

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

	public function getInstance(): Hooks {
		$sut = new Hooks();
		$this->assertInstanceOf( HooksInterface::class, $sut );
		$this->assertInstanceOf( Hooks::class, $sut );
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
	public function itShouldAddListener() {
		$sut = $this->getInstance();

		$args = [
			'event',
			function () {
			},
			10,
			3,
		];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('add_filter', function () use (&$calls, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
		});

		$sut->addListener( ...$args );

		$this->assertEquals( 1, $calls );
	}

	/**
	 * @test
	 */
	public function itShouldRemoveListener() {
		$sut = $this->getInstance();

		$args = [
			'event',
			function () {
			},
			10,
			3,
		];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('remove_filter', function () use (&$calls, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
		});

		$sut->removeListener( ...$args );

		$this->assertEquals( 1, $calls );
	}

	public function argumentsProvider() {
		return [
			'2 params passed'	=> [
				[
					'event_name',
					'arg 1',
				]
			],
			'3 params passed'	=> [
				[
					'event_name',
					'arg 1',
					'arg 2',
				]
			],
			'4 params passed'	=> [
				[
					'event_name',
					'arg 1',
					'arg 2',
					'arg 3',
				]
			],
		];
	}

	/**
	 * @test
	 * @dataProvider argumentsProvider()
	 */
	public function itShouldExecuteWith( $args ) {
		$sut = $this->getInstance();

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('do_action', function () use (&$calls, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
		});

		$sut->execute( ...$args );

		$this->assertEquals( 1, $calls );
	}

	/**
	 * @test
	 * @dataProvider argumentsProvider()
	 */
	public function itShouldFiltersWith( $args ) {
		$sut = $this->getInstance();

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('apply_filters', function () use (&$calls, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
		});

		$sut->filter( ...$args );

		$this->assertEquals( 1, $calls );
	}

	/**
	 * @test
	 */
	public function itShouldReturnCurrentHook() {
		$sut = $this->getInstance();

		$hook_name = 'hook_name';

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('current_filter', function () use (&$calls, $hook_name) {
			$calls++;
			return $hook_name;
		});

		$this->assertEquals( $hook_name, $sut->currentHookName(), '' );
		$this->assertEquals( 1, $calls );
	}

	/**
	 * @test
	 */
	public function itShouldHasListener() {
		$sut = $this->getInstance();

		$return_val = true;

		$args = [
			'event',
			function () {
			},
		];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('has_filter', function () use (&$calls, $return_val, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
			return $return_val;
		});

		$this->assertEquals( $return_val, $sut->hasListener(...$args), '' );
		$this->assertEquals( 1, $calls );
	}

	/**
	 * @test
	 */
	public function itShouldRemoveAllListener() {
		$sut = $this->getInstance();

		$return_val = true;

		$args = [
			'event',
			function () {
			},
		];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('remove_all_filters', function ( string $event_name ) use (&$calls, $return_val, $args): bool {
			$calls++;
			Assert::assertEquals( $args[0], $event_name );
			return $return_val;
		});

		$this->assertEquals( $return_val, $sut->removeAllListener($args[0]), '' );
		$this->assertTrue( $sut->removeAllListener($args[0]), '' );
		$this->assertEquals( 2, $calls );
	}
}
