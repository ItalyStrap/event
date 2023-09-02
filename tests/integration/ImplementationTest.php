<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Tests\IntegrationTestCase;

class ImplementationTest extends IntegrationTestCase
{
    public function testDispatcherSimpleImplementation(): void
    {
        $listenerProvider = new \ItalyStrap\Event\OrderedListenerProvider();

        $event = new \stdClass();

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            $event->name = 'Hello';
        });

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            $event->name .= ' World';
        }, 20);

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $name = (string)$dispatcher->dispatch($event)->name;

        $this->assertSame('Hello World', $name);
    }

    public function testDispatcherWithGlobalStateImplementation(): void
    {
        $listenerProvider = new \ItalyStrap\Event\OrderedListenerProvider();
        $state = new \ItalyStrap\Event\GlobalState();

        $event = new \stdClass();

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            $event->name = 'Hello';
        });

        $listener = new class ($state) {
            private $state;

            public function __construct(\ItalyStrap\Event\GlobalState $state)
            {
                $this->state = $state;
            }
            public function __invoke(object $event)
            {
                $event->name .= ' World';
                $event->currentState = $this->state->currentEventName();
            }
        };

        $listenerProvider->addListener(\stdClass::class, $listener, 20);

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider, $state);

        $name = $dispatcher->dispatch($event)->name;

        $this->assertSame('Hello World', $name);
        $this->assertSame('stdClass', $event->currentState);
    }
}
