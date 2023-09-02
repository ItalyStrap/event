<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\GlobalState;
use ItalyStrap\Tests\GlobalStateTestTrait;
use ItalyStrap\Tests\IntegrationTestCase;
use PHPUnit\Framework\Assert;

class GlobalStateTest extends IntegrationTestCase
{
    use GlobalStateTestTrait;

    public function makeInstance(): GlobalState
    {
        return new GlobalState();
    }

    public function testBeforeDispatchingEvent()
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
}
