<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\DebugDispatcher;
use ItalyStrap\Tests\UnitTestCase;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;

class DebugDispatcherTest extends UnitTestCase
{
    private function makeInstance(): DebugDispatcher
    {
        $sut = new DebugDispatcher(
            $this->makePsrDispatcher(),
            $this->makeLogger()
        );
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldDispatchAndRecordLog()
    {
        $event = new stdClass();

        $sut = $this->makeInstance();

        $this->logger
            ->debug(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalled();

        $this->psrDispatcher
            ->dispatch(Argument::type('object'))
            ->willReturn($event);

        $actual = $sut->dispatch($event);
        $this->assertSame($event, $actual, 'It should return the same event');
    }
}
