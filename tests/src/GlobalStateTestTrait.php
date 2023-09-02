<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\StateInterface;
use PHPUnit\Framework\Assert;

trait GlobalStateTestTrait
{
    public function testGlobalStateWithoutCallingForEvent()
    {
        $sut = $this->makeInstance();
        Assert::assertEmpty(
            $sut->currentEvent(),
            'Current event should be empty if forEvent() is not called'
        );

        Assert::assertFalse(
            $sut->dispatchingEvent(),
            'Should be dispatching event if forEvent() is not called'
        );

        Assert::assertSame(
            0,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 0 if forEvent() is not called'
        );
    }

    public function testGlobalStateWithCallingForEvent()
    {
        $sut = $this->makeInstance();
        $sut->forEvent('foo');

        $sut->progress(StateInterface::BEFORE);

        Assert::assertSame(
            1,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 0 if forEvent() is called'
        );

        Assert::assertSame(
            'foo',
            $sut->currentEvent(),
            'Current event should be foo if forEvent() is called with foo'
        );

        Assert::assertTrue(
            $sut->dispatchingEvent(),
            'Should be dispatching event if forEvent() is called'
        );
    }
}
