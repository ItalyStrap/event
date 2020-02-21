<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Event\PsrDispatcher\CallableFactory;
use UnitTester;

/**
 * Class CallableFactoryTest
 * @package ItalyStrap\Tests
 */
class CallableFactoryTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
    protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	private function getInstance() {
		$sut = new CallableFactory();
		$this->assertInstanceOf( CallableFactory::class, $sut, '' );
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
	public function itShouldReturn() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldBuildCallable() {
		$sut = $this->getInstance();
		$sut->buildCallable( function () {
		} );
	}
}
