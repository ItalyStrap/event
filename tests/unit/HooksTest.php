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
class HooksTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
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
	public function itShouldbeInstantiable(  ) {
		$sut = $this->getInstance();
    }

	/**
	 * @test
	 */
	public function itShouldAddListener(  ) {
		$sut = $this->getInstance();

		$args = [
			'event',
			function () {},
			10,
			3,
		];

		FunctionMockerLe\define('add_filter', function () use (&$calls, $args) {
			$calls++;
			Assert::assertEquals($args, func_get_args());
		});

		$sut->addListener( ...$args );

		$this->assertEquals( 1, $calls );
    }
}
