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
}
