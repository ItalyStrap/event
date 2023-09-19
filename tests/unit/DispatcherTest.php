<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Unit;

use ItalyStrap\Event\Dispatcher;
use ItalyStrap\Tests\UnitTestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

class DispatcherTest extends UnitTestCase
{
    private function makeInstance(): Dispatcher
    {
        return new Dispatcher(
            new class implements ListenerProviderInterface {
                public function getListenersForEvent(object $event): iterable
                {
                    return [];
                }
            }
        );
    }

    public function testDispatch(): void
    {
        $event = new \stdClass();
        $sut = $this->makeInstance();
        $this->assertSame($event, $sut->dispatch($event));
    }
}
