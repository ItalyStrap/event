<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Tests\IntegrationTestCase;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\StoppableEventInterface;

class ImplementationTest extends IntegrationTestCase
{
    /**
     * In this test wa are not using any implementation of the StateInterface
     * this means that `current_filter()`, `doing_filter` and `did_action()` will return a default value
     * because the globals declared by WordPress are not set.
     *
     * Pay attention if you call the WordPress API `\do_action()`
     * after the `::dispatch()` method like in this example:
     * `\do_action(\stdClass::class, $event);`
     * the globals will be set and the test will fail.
     */
    public function testDispatcherStatelessSimpleImplementation(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();

        $event = new \stdClass();

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            Assert::assertEmpty(\current_filter(), 'Current filter should be empty');
            Assert::assertFalse(\doing_filter(\stdClass::class), 'Doing action should return false');
            Assert::assertSame(0, \did_action(\stdClass::class), 'Did action should return 0');
            $event->name = 'Hello';
        });

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            Assert::assertEmpty(\current_filter(), 'Current filter should be empty');
            Assert::assertFalse(\doing_filter(\stdClass::class), 'Doing action should return false');
            Assert::assertSame(0, \did_action(\stdClass::class), 'Did action should return 0');
            $event->name .= ' World';
        }, 20);

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $name = (string)$dispatcher->dispatch($event)->name;

        $this->assertSame('Hello World', $name);
        Assert::assertSame(0, \did_action(\stdClass::class), 'Did action should return 0');
    }

    /**
     * In this test we are using the GlobalState implementation of the StateInterface
     * this means that `current_filter()`, `doing_filter` and `did_action()` will return the correct value
     * because the globals declared by WordPress are set.
     * This is the same as the previous test but with the GlobalState implementation.
     *
     * Now if you call the WordPress API `\do_action()`
     * after the `::dispatch()` method like in this example:
     * `\do_action(\stdClass::class, $event);`
     * the `did_action()` will be incremented as expected.
     *
     * In this case both API act in the same way.
     */
    public function testDispatcherWithGlobalStateImplementation(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
        $state = new \ItalyStrap\Event\GlobalState();

        $event = new \stdClass();

        $listenerProvider->addListener(\stdClass::class, function (object $event) {
            $event->name = 'Hello';
        });

        $listener = new class ($state) {
            private \ItalyStrap\Event\GlobalState $state;

            public function __construct(\ItalyStrap\Event\GlobalState $state)
            {
                $this->state = $state;
            }

            public function __invoke(object $event)
            {
                Assert::assertSame(
                    \get_class($event),
                    \current_filter(),
                    'Current event name should be equal to the event name'
                );
                Assert::assertTrue(
                    \doing_filter(\get_class($event)),
                    'Doing action should return true'
                );

                Assert::assertSame(
                    \current_filter(),
                    $this->state->currentEventName(),
                    'Current event name should be equal to the current filter'
                );

                Assert::assertSame(
                    \doing_filter(\get_class($event)),
                    $this->state->isDispatching(),
                    'Doing filter should be equal to the isDispatching'
                );

                $event->name .= ' World';
                $event->currentState = $this->state->currentEventName();
            }
        };

        $listenerProvider->addListener(\stdClass::class, $listener, 20);

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider, $state);


        $name = $dispatcher->dispatch($event)->name;

        $this->assertSame('Hello World', $name, 'Expected name should be equal to Hello World');
        $this->assertSame('stdClass', $event->currentState);
        Assert::assertSame(1, \did_action(\stdClass::class), 'Did action should return 1');
        Assert::assertSame(1, $state->dispatchedEventCount(), 'Did action should return 1');

        \do_action(\stdClass::class, $event);

        $this->assertSame('Hello World', $name, 'Expected name should be equal to Hello World');
        Assert::assertSame(2, \did_action(\stdClass::class), 'Did action should return 2');
        Assert::assertSame(2, $state->dispatchedEventCount(), 'Did action should return 2');
    }

    /**
     * In this test is similar to the previous one but here we're using the StoppableEventInterface
     * As you can see the StoppableEventInterface works only with the PSR-14 implementation
     * Calling `do_action()` an event that implements the StoppableEventInterface will not stop further execution
     * because the `do_action()` is not aware of the StoppableEventInterface.
     * `do_action()` will continue to execute all the listeners in the stack.
     */
    public function testDispatcherWithGlobalImplementationAndStoppableImplementation(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
        $state = new \ItalyStrap\Event\GlobalState();

        $event = new class implements StoppableEventInterface {
            public bool $stopped = false;
            public string $name = '';
            public function isPropagationStopped(): bool
            {
                return $this->stopped;
            }
        };

        $eventName = \get_class($event);

        $listenerProvider->addListener($eventName, function (object $event) {
            $event->name = 'Hello';
        }, 10);

        $listenerProvider->addListener($eventName, function (object $event) {
            $event->stopped = true;
        }, 11);

        $listenerProvider->addListener($eventName, function (object $event) {
            $event->name .= ' World';
        }, 12);

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider, $state);

        $name = $dispatcher->dispatch($event)->name;

        $this->assertSame('Hello', $name, 'The event should be stopped');
        Assert::assertSame(1, $state->dispatchedEventCount(), 'Did action should return 1');

        $name = $dispatcher->dispatch($event)->name;

        $this->assertSame('Hello', $name, 'The event should be stopped');
        Assert::assertSame(2, $state->dispatchedEventCount(), 'Did action should return 2');

        \do_action($eventName, $event);

        $this->assertSame('Hello World', $event->name, 'The event should not be stopped');
        Assert::assertSame(3, $state->dispatchedEventCount(), 'Did action should return 3');
    }
}
