<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Config\Config;
use ItalyStrap\Empress\AurynResolverInterface;
use ItalyStrap\Empress\Extension;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\EventResolverExtension;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

// phpcs:disable
require_once codecept_data_dir( '/fixtures/classes.php' );
// phpcs:enable
class ResolverExtensionTest extends Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;
	/**
	 * @var ObjectProphecy
	 */
	private $fake_injector;

	/**
	 * @var ObjectProphecy
	 */
	private $event_manager;

	/**
	 * @var ObjectProphecy
	 */
	private $config;

	/**
	 * @return Config
	 */
	public function getConfig(): Config {
		return $this->config->reveal();
	}

	/**
	 * @return EventManager
	 */
	public function getEventManager(): EventManager {
		return $this->event_manager->reveal();
	}

	/**
	 * @return Injector
	 */
	public function getFakeInjector(): Injector {
		return $this->fake_injector->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->fake_injector = $this->prophesize( Injector::class );
		$this->event_manager = $this->prophesize( EventManager::class );
		$this->config = $this->prophesize( Config::class );
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
	public function callbackShouldSubscribeListenersWithIndexedArray() {
		$subscriber = $this->prophesize( Subscriber::class );

		$this->event_manager->addSubscriber( $subscriber->reveal() )->shouldBeCalled();
		$this->config->get()->shouldNotBeCalled();

		$this->fake_injector->share( Argument::type('string'))
			->willReturn( $this->getFakeInjector() )
			->shouldBeCalled();

		$this->fake_injector->make( Argument::type('string'))
			->willReturn( $subscriber->reveal() )
			->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->walk( Subscriber::class, 0, $this->getFakeInjector() );
	}

	/**
	 * @test
	 */
	public function callbackShouldSubscribeListenersFormAssociativeArrayWithTrueOptionKey() {
		$subscriber = $this->prophesize( Subscriber::class );
		$config = [
			'key'	=> true
		];
		$key = \array_keys( $config )[0];

		$this->event_manager->addSubscriber( $subscriber->reveal() )->shouldBeCalled();
		$this->config->get( $key, false )->willReturn($config[$key])->shouldBeCalled();

		$this->fake_injector->share( Argument::type('string'))
			->willReturn( $this->getFakeInjector() )
			->shouldBeCalled();

		$this->fake_injector->make( Argument::type('string'))
			->willReturn( $subscriber->reveal() )
			->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->walk( Subscriber::class, $key, $this->getFakeInjector() );
	}

	/**
	 * @test
	 */
	public function callbackShouldNotSubscribeListenersFromAssociativeArrayWithFalseOptionKey() {
		$subscriber = $this->prophesize( Subscriber::class );
		$config = [
			'key'	=> false
		];
		$key = \array_keys( $config )[0];

		$this->event_manager->addSubscriber( $subscriber->reveal() )->shouldNotBeCalled();
		$this->config->get( $key, false )->willReturn($config[$key])->shouldBeCalled();

		$this->fake_injector->share( Argument::type('string'))
			->willReturn( $this->getFakeInjector() )
			->shouldNotBeCalled();

		$this->fake_injector->make( Argument::type('string'))
			->willReturn( $subscriber->reveal() )
			->shouldNotBeCalled();

		$sut = $this->getInstance();
		$sut->walk( Subscriber::class, $key, $this->getFakeInjector() );
	}

	/**
	 * @test
	 */
	public function itShouldExecute() {
		$application = $this->prophesize(AurynResolverInterface::class);

		$application->walk(Argument::type('string'), Argument::type('callable'))->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->execute($application->reveal());
	}
}
