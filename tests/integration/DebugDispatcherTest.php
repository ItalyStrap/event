<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Integration;

use ItalyStrap\Event\DebugDispatcher;
use ItalyStrap\Event\Dispatcher;
use ItalyStrap\Tests\IntegrationTestCase;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\NullLogger;

class DebugDispatcherTest extends IntegrationTestCase
{
    private function makeInstance(): DebugDispatcher
    {
        return new DebugDispatcher(
            new Dispatcher(new class implements ListenerProviderInterface {
                public function getListenersForEvent(object $event): iterable
                {
                    return [];
                }
            }),
            new NullLogger()
        );
    }

    public function testItShouldDispatch()
    {
        $event = new \stdClass();

        $sut = $this->makeInstance();

        $actual = $sut->dispatch($event);
        Assert::assertSame($event, $actual, 'It should return the same event');
    }
}
