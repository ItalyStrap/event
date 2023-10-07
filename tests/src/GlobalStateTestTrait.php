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
            $sut->currentEventName(),
            'Current event should be empty if forEvent() is not called'
        );

        Assert::assertFalse(
            $sut->isDispatching(),
            'Should be false if forEvent() is not called'
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

        $sut->forEvent(new \stdClass(), $this->makeDispatcher());
        $sut->progress(StateInterface::BEFORE, $this->makeDispatcher());

        Assert::assertSame(
            1,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 0 if forEvent() is called'
        );

        Assert::assertSame(
            'stdClass',
            $sut->currentEventName(),
            'Current event should be foo if forEvent() is called with foo'
        );

        Assert::assertTrue(
            $sut->isDispatching(),
            'Should be dispatching event if forEvent() is called'
        );
    }

    public function testGlobalStateCount()
    {
        $sut = $this->makeInstance();

        $sut->forEvent(new \stdClass(), $this->makeDispatcher());
        $sut->progress(StateInterface::BEFORE, $this->makeDispatcher());

        $sut->forEvent(new \stdClass(), $this->makeDispatcher());
        $sut->progress(StateInterface::BEFORE, $this->makeDispatcher());

        Assert::assertSame(
            2,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 2'
        );
    }

    public function testGlobalStateWithCallingForEventAndProgressbeforeAfter()
    {
        $sut = $this->makeInstance();

        $sut->forEvent(new \stdClass(), $this->makeDispatcher());
        $sut->progress(StateInterface::BEFORE, $this->makeDispatcher());

        Assert::assertSame(
            1,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 1 if forEvent() is called'
        );

        Assert::assertSame(
            'stdClass',
            $sut->currentEventName(),
            'Current event should be stdClass if forEvent() is called with stdClass'
        );

        Assert::assertTrue(
            $sut->isDispatching(),
            'Should be dispatching event'
        );

        $sut->progress(StateInterface::AFTER, $this->makeDispatcher());

        Assert::assertSame(
            1,
            $sut->dispatchedEventCount(),
            'Dispatched event count should be 1 if forEvent() is called'
        );
    }
}
