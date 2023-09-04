<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Tests\EventForRenderer;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\RendererAsEvent;
use ItalyStrap\Tests\RendererWithEvent;
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

        Assert::assertSame('Hello World', $name);
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

        Assert::assertSame('Hello World', $name, 'Expected name should be equal to Hello World');
        Assert::assertSame('stdClass', $event->currentState);
        Assert::assertSame(1, \did_action(\stdClass::class), 'Did action should return 1');
        Assert::assertSame(1, $state->dispatchedEventCount(), 'Did action should return 1');

        \do_action(\stdClass::class, $event);

        Assert::assertSame('Hello World', $name, 'Expected name should be equal to Hello World');
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

        Assert::assertSame('Hello', $name, 'The event should be stopped');
        Assert::assertSame(1, $state->dispatchedEventCount(), 'Did action should return 1');

        $name = $dispatcher->dispatch($event)->name;

        Assert::assertSame('Hello', $name, 'The event should be stopped');
        Assert::assertSame(2, $state->dispatchedEventCount(), 'Did action should return 2');

        \do_action($eventName, $event);

        Assert::assertSame('Hello World', $event->name, 'The event should not be stopped');
        Assert::assertSame(3, $state->dispatchedEventCount(), 'Did action should return 3');
    }

    /**
     * Example is taken here https://gist.github.com/westonruter/6647252
     * In this test we can see an example on how you can remove and add again a filter
     * to some entity in WordPress, this is needed because you do not want to have the changes
     * in all event names but only in one specific event, so the other events will not be affected.
     */
    public function testAddAndRemoveFilterInWordPressEnv()
    {
        // I add this because in the test env the `wptexturize` filter is not yet added.
        \add_filter('the_title', 'wptexturize');

        // Just create a Fake post
        $entityId = \wp_insert_post(
            [
                'post_title'    => "'cause today's effort makes it worth tomorrow's \"holiday\"",
                'post_content'  => 'Hello World',
            ]
        );

        // Make sure the filter is added
        Assert::assertSame(
            "&#8217;cause today&#8217;s effort makes it worth tomorrow&#8217;s &#8220;holiday&#8221;",
            \get_the_title($entityId)
        );

        \remove_filter('the_title', 'wptexturize');
        $title = get_the_title($entityId);
        Assert::assertSame("'cause today's effort makes it worth tomorrow's \"holiday\"", $title);
        \add_filter('the_title', 'wptexturize');

        // Add again the filter to revert the default behaviour
        $title = get_the_title($entityId);
        Assert::assertSame(
            "&#8217;cause today&#8217;s effort makes it worth tomorrow&#8217;s &#8220;holiday&#8221;",
            \get_the_title($entityId)
        );
    }

    /**
     * We can do the same as the previous test but using the PSR-14 implementation
     * For this test I use a Renderer but the same can be done with any other events.
     * Take a look at this test, as you can see we're trying to remove a listener
     * to revert the behaviour of the renderer but because the $mockRenderer is passed by reference
     * even if we remove the listener the object s already modified and passed as is.
     * If we want to remove a listener we need to instantiate a new RendererAsEvent object.
     */
    public function testAddAndRemoveListenerForRendererAsEvent(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $mockRenderer = new RendererAsEvent($dispatcher);

        Assert::assertSame('Hello World', $mockRenderer->render());

        $listener = function (object $event) {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->addListener(\get_class($mockRenderer), $listener, 10);

        Assert::assertSame('Hello there', $mockRenderer->render());

        // This won't take effect because the $mockRenderer is passed by reference because it's an object.
        $listenerProvider->removeListener(\get_class($mockRenderer), $listener);

        Assert::assertSame('Hello there', $mockRenderer->render());
    }

    /**
     * This example is similar to the previous one but in this case we created a dedicated event to be
     * instantiated inside the render method of the RendererWithEvent class.
     * In this case because the event is instantiated every time the render method is called
     * if we remove the listener we can revert the behaviour of the RendererWithEvent::render() method as the default.
     */
    public function testAddAndRemoveListenerForRendererWithEvent(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $mockRenderer = new RendererWithEvent($dispatcher);

        Assert::assertSame('Hello World', $mockRenderer->render());

        $listener = function (object $event) {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->addListener(EventForRenderer::class, $listener);

        Assert::assertSame('Hello there', $mockRenderer->render());

        // This time it will take effect because EventForRenderer::class in instantiated inside the dispatcher
        // every time the dispatch() method is called.
        $listenerProvider->removeListener(EventForRenderer::class, $listener);

        Assert::assertSame('Hello World', $mockRenderer->render());
    }
}
