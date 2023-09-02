<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Unit;

use ItalyStrap\Event\Dispatcher;
use ItalyStrap\Event\NullListenerProvider;
use ItalyStrap\Tests\UnitTestCase;

class DispatcherTest extends UnitTestCase
{
    private function makeInstance(): Dispatcher
    {
        return new Dispatcher(
            new NullListenerProvider()
        );
    }

    public function testDispatch(): void
    {
        $event = new \stdClass();
        $sut = $this->makeInstance();
        $this->assertSame($event, $sut->dispatch($event));
    }
}
