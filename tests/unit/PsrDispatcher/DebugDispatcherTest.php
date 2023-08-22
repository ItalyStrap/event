<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\PsrDispatcher\DebugDispatcher;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use stdClass;
use UnitTester;

class DebugDispatcherTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    private ?\Prophecy\Prophecy\ObjectProphecy $dispatcher = null;

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher->reveal();
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger->reveal();
    }
    private ?\Prophecy\Prophecy\ObjectProphecy $logger = null;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    /**
     * @return DebugDispatcher
     */
    private function getInstance()
    {
        $sut = new DebugDispatcher(
            $this->getDispatcher(),
            $this->getLogger()
        );
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $sut = $this->getInstance();
    }

    /**
     * @test
     */
    public function itShouldDispatchAndRecordLog()
    {
        $sut = $this->getInstance();

        $this->logger
            ->debug(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalled();

        $this->dispatcher->dispatch(Argument::type('object'))->shouldBeCalled();

        $sut->dispatch(new stdClass());
    }
}
