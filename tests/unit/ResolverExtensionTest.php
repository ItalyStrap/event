<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Empress\Extension;
use ItalyStrap\Event\EventResolverExtension;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/classes.php' );
// phpcs:enable
class ResolverExtensionTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $fake_injector;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $event_manager;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $config;

	/**
	 * @return \ItalyStrap\Config\Config
	 */
	public function getConfig(): \ItalyStrap\Config\Config {
		return $this->config->reveal();
	}

	/**
	 * @return \ItalyStrap\Event\EventManager
	 */
	public function getEventManager(): \ItalyStrap\Event\EventManager {
		return $this->event_manager->reveal();
	}

	/**
	 * @return \ItalyStrap\Empress\Injector
	 */
	public function getFakeInjector(): \ItalyStrap\Empress\Injector {
		return $this->fake_injector->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->fake_injector = $this->prophesize( \ItalyStrap\Empress\Injector::class );
		$this->event_manager = $this->prophesize( \ItalyStrap\Event\EventManager::class );
		$this->config = $this->prophesize( \ItalyStrap\Config\Config::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	protected function getInstance(): EventResolverExtension {
		$sut = new EventResolverExtension( $this->getEventManager(), $this->getConfig() );
		$this->assertInstanceOf( Extension::class, $sut, '' );
		$this->assertInstanceOf( EventResolverExtension::class, $sut, '' );
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
	public function itShouldHaveName() {
		$sut = $this->getInstance();
		$this->assertStringContainsString( EventResolverExtension::KEY, $sut->name(), '' );
	}

	/**
	 * @test
	 */
	public function callbackShouldDoTheJobOfSubscribeListenersWithIndexedArray() {
		$subscriber = $this->prophesize(\ItalyStrap\Tests\Subscriber::class );

		$this->event_manager->addSubscriber( $subscriber->reveal() )->shouldBeCalled();
		$this->config->get()->shouldNotBeCalled();

		$this->fake_injector->share(\Prophecy\Argument::type('string'))
			->willReturn( $this->getFakeInjector() )
			->shouldBeCalled();

		$this->fake_injector->make(\Prophecy\Argument::type('string'))
			->willReturn( $subscriber->reveal() )
			->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->walk( \ItalyStrap\Tests\Subscriber::class, 0, $this->getFakeInjector() );
	}

	/**
	 * @test
	 */
//    public function callbackShouldDoTheJobOfSubscribeListenersWithAssociativedArray()
//    {
//		$subscriber = $this->prophesize(\ItalyStrap\Tests\Subscriber::class );
//
//		$this->event_manager->addSubscriber( $subscriber->reveal() )->shouldBeCalled();
//		$this->config->get()->shouldNotBeCalled();
//
//		$this->fake_injector->share(\Prophecy\Argument::type('string'))
//			->willReturn( $this->getFakeInjector() )
//			->shouldBeCalled();
//
//		$this->fake_injector->make(\Prophecy\Argument::type('string'))
//			->willReturn( $subscriber->reveal() )
//			->shouldBeCalled();
//
//		$sut = $this->getInstance();
//		$sut->walk( \ItalyStrap\Tests\Subscriber::class, '0', $this->getFakeInjector() );
//    }
}
