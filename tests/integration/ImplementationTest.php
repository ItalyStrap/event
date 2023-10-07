<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use Crell\Tukio\OrderedListenerProvider;
use Fig\EventDispatcher\AggregateProvider;
use Fig\EventDispatcher\TaggedProviderTrait;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Tests\EventForRenderer;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\RendererAsEvent;
use ItalyStrap\Tests\RendererWithEvent;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class ImplementationTest extends IntegrationTestCase
{
    /**
     * First we need to learn the concept of the WordPress Hooks API and how they work because after that we can
     * create connection between the WordPress Hooks API and the PSR-14 implementation.
     *
     * The good news is that the WordPress Hooks API are very simple and very powerful.
     * The bad news is that the WordPress Hooks API use globals variables and this is a very bad practice.
     *
     * Let's start with how the WordPress Hooks API 'dispatch' stuff.
     * In the API we have two main functions:
     * - `do_action()`
     * - `apply_filters()`
     * Those two functions act as a dispatcher for the WordPress Hooks API.
     *
     * They accept n arguments, the first argument is the name of the event name (hook) with the type of string.
     * The other arguments are the arguments that will be passed to the listeners (callbacks) in the stack, and
     * they can be of any type, but is the second argument that is the most important, this is the argument that
     * will be changed (and returned in the cse of `apply_filters()`) by the listeners (callbacks) in the stack.
     *
     * What they do is to call all the listeners (callbacks) in the stack registered for a specific event name (hooks)
     * registered and then apply the changes to the value passed to the function (callback).
     * Important to remember is that all the listeners are called again if you call the same function again.
     * So the loop will be executed every time you call the function `do_action()` or `apply_filters()`.
     * If you call them multiple times the listeners will be executed multiple times.
     *
     * There is no way to stop the execution of the listeners in the stack, they will be executed all the time.
     * No, I lied to you, in fact we have a way to stop the execution of the listeners in the stack, we can remove
     * one or all listeners appended to an event name, but will see that later.
     *
     * Now you know that both `do_action()` and `apply_filters()` 'execute' all the listeners in the stack
     * but with a little difference between them.
     * `do_action()` will not return any value, it will just execute the listeners in the stack and vice versa
     * `apply_filters()` will return the value passed to the function after all the listeners are executed.
     *
     * So now you know how to dispatch an event (execute callback to a hook) in WordPress, but how can you register
     * a or many listeners to a specific event name (hook)?
     *
     * To register a listener to a specific event name (hook) you can use the `add_action()`
     * or `add_filter()` functions.
     * Both function are the same, `add_action()` is just a wrapper for `add_filter()`, the `add_action()` function
     * is here only to have some domain specific language, so you can use `add_action()` to register a listener,
     * but you can also use `add_filter()` to do the same thing.
     *
     * Those functions accept 4 arguments:
     * - $hook_name (string) (Required) The name of the event to which the $callback is hooked.
     * - $callback (callable) (Required) The callback to be run when the event is fired.
     * - $priority (int) (Optional) Used to specify the order in which the functions associated with a particular
     *  event are executed. Lower numbers correspond with earlier execution, and functions with the same priority
     * are executed in the order in which they were added to the action. The default value is 10.
     * - $accepted_args (int) (Optional) The number of arguments the function accepts. The default is 1.
     *
     * One thing to note here about the `$accepted_args`, with the default value as 1 means that at least one argument
     * could be passed to the callback, but if we see under the hood on how this is implemented we can see that
     * if the argument is 0 the callback will be passed to the `call_user_func()`
     * like this `call_user_func( $the_['function'] )`,
     * but if the `$accepted_args` is >= (major or equal) to the number of arguments passed to the function
     * will be used the `call_user_func_array()` like this `call_user_func_array( $the_['function'], $args )`.
     *
     * Now this means that you could use any number major or equal to the number accepted by the callback
     * even PHP_MAX_INT if you want, `call_user_func_array()` will not complain about that.
     *
     * If you want to call `call_user_func()` you need to add 0 as the `$accepted_args` argument.
     * This happens if you for example dispatch an event with only the event name and no other arguments.
     * `do_action('event_name');` or `apply_filters('event_name');`
     *
     * (Even if in this case they do the same thing it is better to call `do_action()` to dispatch this event,
     * a case could be to run some code in the stack without the need to have a value, loggin, echoing, and other
     * kind of side effects.)
     *
     * If the number is not 0 and is < (minor) to the number of arguments passed to the function
     * will be used the `call_user_func_array()` like this
     * `call_user_func_array( $the_['function'], array_slice( $args, 0, (int) $the_['accepted_args'] ) )` a little
     * more expensive than the previous one.
     *
     * So, after this long comment let's see how we can register a listener to a specific event name (hook) and
     * how we can dispatch an event.
     */
    public function testRegisterAndDispatchingTheWordPressWay()
    {
        // Let's register a listener to a specific event name (hook) 'event_name_for_filter'
        // As I said before we can use `add_action()` or `add_filter()` to do the same thing.
        // so let's use `add_filter()`.
        \add_filter(
            'event_name_for_filter', // The name of the event to which the $callback is hooked.
            // The callback to be run when the event is fired.
            function (string $value) {
            // For this example the callback accept only one argument
                // Because we use `apply_filters()` we need to return the value
                return $value . ' World'; // In this example we just append ' World' to the value we received
            }
        );

        // Now we can dispatch the event
        $value = \apply_filters('event_name_for_filter', 'Hello');
        // And we assert that the value is equal to 'Hello World'
        Assert::assertSame('Hello World', $value);
        // Normally `apply_filters()` is used in functions that return a value like for example `the_title()`

        // Now let's register another listener to the event name (hook) 'event_name_for_action'
        // but this time we do not return the value because we use `do_action()` later.
        \add_filter(
            'event_name_for_action', // The name of the event to which the $callback is hooked.
            // The callback to be run when the event is fired.
            function (string $value) {
            // For this example the callback accept only one argument
                echo $value . ' World'; // In this example we just append ' World' to the value we received
                // Because we use `do_action()` we do not need to return the value
                // In this example we do side effects like echoing.
            }
        );

        $arg = 'Hello';
        // We need to sniff the output of the callback to see if it works as expected
        $this->expectOutputString('Hello World');
        \do_action('event_name_for_action', $arg);
        // Normally `do_action()` is used in functions that do not return a value like for example `wp_head()`
        // or in case you need to perform some saving or other side effects.
        // in pseudocode:
        // if $args is equal to 'Hello' then do stuff
        // The `$args` is not changed in this case because the value was a string and not an object.
        Assert::assertSame('Hello', $arg);

        // This will happen with all type of values passed to the callback but not with objects.
        // Objects are passed by reference so if you change the object in the callback the object will be changed
        // also in the caller (the caller is the function that dispatch the event).
        // Let's see an example:
        $arg = new \stdClass();
        $arg->name = 'Hello';
        \add_filter(
            'event_name_for_action_with_object', // The name of the event to which the $callback is hooked.
            // The callback to be run when the event is fired.
            function (\stdClass $value) {
            // For this example the callback accept only one argument
                $value->name .= ' World'; // In this example we just append ' World' to the value we received
                // Because we use `do_action()` we do not need to return the value
                // In this example we do side effects like echoing.
            }
        );
        \do_action('event_name_for_action_with_object', $arg);
        // Now the $arg->name is changed because the object is passed by reference.
        Assert::assertSame('Hello World', $arg->name);

        // We can do the same with `apply_filters()` and objects.
        $arg = new \stdClass();
        $arg->name = 'Hello';
        \add_filter(
            'event_name_for_filter_with_object', // The name of the event to which the $callback is hooked.
            // The callback to be run when the event is fired.
            function (\stdClass $value) {
            // For this example the callback accept only one argument
                $value->name .= ' World'; // In this example we just append ' World' to the value we received
                // Because we use `do_action()` we do not need to return the value
                // In this example we do side effects like echoing.
                return $value;
            }
        );
        $arg = \apply_filters('event_name_for_filter_with_object', $arg);
        // Now the $arg->name is changed because the object is passed by reference.
        Assert::assertSame('Hello World', $arg->name);

        // Fun fact: because we use an object and `apply_filters()` return a value we could do something like this:
        Assert::assertSame(
            'Hello World World', // This is the expected value because we have two listeners in the stack
            \apply_filters('event_name_for_filter_with_object', $arg)->name
        );

        // But because `apply_filters()` can return any type of value (even unicorns Cit.) not only objects
        // please do not do this until you know what you are doing, but still,
        // do not do this even if you know what you're doing because let's say that you have a listener that return a
        // string and another listener that return an object, what will happen?
        // Let's see:
        \add_filter(
            'event_name_for_filter_with_object',
            function (string $value) {
                return 'Something that is not an object';
            }
        );

        // I need to wrap this in a closure and do assertion to avoid TypeError
        $this->tester->expectThrowable(
            \TypeError::class,
            function () use ($arg) {
                // This will throw a TypeError because the first listener return a string and not an object
                // as we could expect.
                \apply_filters('event_name_for_filter_with_object', $arg)->name;
            }
        );

        // The later example was only an introduction to what we can do with the PSR-14 implementation, but
        // I'll explain more later.
    }

    /**
     * In this test wa are not using any implementation of the StateInterface
     * this means that `current_filter()`, `doing_filter` and `did_action()` will return a default value
     * because the globals declared by WordPress are not set.
     *
     * Pay attention if you call the WordPress Hooks API `\do_action()`
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
     * Now if you call the WordPress Hooks API `\do_action()`
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
            use \ItalyStrap\Event\PropagationAwareTrait;

            public string $name = '';
        };

        $eventName = \get_class($event);

        $listenerProvider->addListener($eventName, function (object $event) {
            $event->name = 'Hello';
        }, 10);

        $listenerProvider->addListener($eventName, function (object $event) {
            \method_exists($event, 'stopPropagation') and $event->stopPropagation();
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

    /**
     * This example shows a different way to use the dispatcher connected to the WordPress Hooks API.
     * The simplest way is to register the listener with the `add_filter()` function, in this example
     * we're using an external package to register the listener, we extend it, and we use the `add_filter()` function
     * inside the `addListener()` method.
     * This way we can be sure our listener is registered both in
     * the WordPress Hooks API and in the PSR-14 implementation.
     *
     * With this method we can use both `\do_action()` and `$dispatcher->dispatch()` to trigger the event.
     * But
     */
    public function testWordPressListenerWithExternalPackage(): void
    {
        $listenerProvider = new class extends OrderedListenerProvider implements ListenerProviderInterface {
            public function addListenerFromCallable(
                callable $listener,
                ?int $priority = null,
                ?string $id = null,
                ?string $type = null
            ): string {
                \add_filter(
                    $type,
                    $listener,
                    $priority ?? 10,
                );
                return parent::addListener($listener, $priority, $id, $type);
            }
        };

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $event = new EventForRenderer();

        Assert::assertSame('Hello World', $event->rendered);

        $listener = function (object $event): void {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->addListenerFromCallable($listener, null, null, EventForRenderer::class);

        Assert::assertSame('Hello there', $dispatcher->dispatch($event)->rendered);

        $event = new EventForRenderer();
        \do_action(EventForRenderer::class, $event);
        Assert::assertSame('Hello there', $event->rendered);

        $event = new EventForRenderer();
        $value = \apply_filters(EventForRenderer::class, $event);
        Assert::assertSame('Hello there', $event->rendered);
        // Because the $listener callback does not return a value the $value will be null
        Assert::assertNull($value, 'The return value of the filter should be null');
    }

    /**
     * If you want to use string event name you can still do it with `addListener()` method
     * because the `addListener()` method use the `add_filter()` function to register the listener,
     * but pay attention, if you want to dispatch the event you need to use one of the WordPress Hooks API
     * `do_action()` or `apply_filters()`.
     *
     * The simple explanation is that the `dispatch()` method is only aware of the event as object
     * and under the hood when loop the stack of listeners only a listener that match
     * the event object name will be executed.
     */
    public function testAddListenerForRendererWithEventString(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $listener = function (object $event) {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->addListener('event_name', $listener);

        $event = new EventForRenderer();
        \do_action('event_name', $event);

        Assert::assertSame('Hello there', $event->rendered);

        $dispatcher->dispatch($event);
        // As you can see the `dispatch()` method does not change the event.
        Assert::assertSame('Hello there', $event->rendered);
    }

    /**
     * Now this example shows the possibility to use an alias for the event name, so instead of using
     * the object event name you can use a string event name and bind it to the object event name.
     * Right now is still experimental, need to be tested more.
     *
     * But this could be dangerous because if you bind for example an event name to a string that is already
     * used by classic `do_action` or `apply_filters()` you could have some unexpected behaviour, just to name a few:
     * - the_title
     * - the_content
     * - the_excerpt
     * and so on.
     */
    public function testAddListenerForRendererWithEventStringAsAlias(): void
    {
        $listenerProvider = new class implements ListenerProviderInterface {
            private ListenerProviderInterface $listenerProvider;
            private array $aliases = [];

            public function __construct()
            {
                $this->listenerProvider = new GlobalOrderedListenerProvider();
            }

            public function alias(string $alias, string $eventName): void
            {
                $this->aliases[$eventName] = $alias;
            }

            public function addListener(string $eventName, callable $listener, int $priority = 10): bool
            {
                return $this->listenerProvider->addListener($eventName, $listener, $priority);
            }

            public function getListenersForEvent(object $event): iterable
            {
                global $wp_filter;
                $callbacks = [];
                $eventName = \get_class($event);
                $eventName = $this->aliases[$eventName] ?? $eventName;

                if (!\array_key_exists($eventName, $wp_filter)) {
                    return $callbacks;
                }

                if (!$wp_filter[$eventName] instanceof \WP_Hook) {
                    return $callbacks;
                }

                foreach ($wp_filter[$eventName]->callbacks as $callbacks) {
                    foreach ($callbacks as $callback) {
                        yield $callback['function'];
                    }
                }
            }
        };

        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $listener = function (object $event) {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->alias('event_name', EventForRenderer::class);
        $listenerProvider->addListener('event_name', $listener);

        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        // This event name is aliased so calling the `do_action()` with alias name will change the event
        \do_action('event_name', $event);
        // As you can see the event is changed
        Assert::assertSame('Hello there', $event->rendered);

        // Revert the event name to the original
        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        // Because is aliased now the `dispatch()` method is triggered.
        $dispatcher->dispatch($event);
        // And the event is changed
        Assert::assertSame('Hello there', $event->rendered);

        /**
         * Just a reminder, because we add an alias name to `add_filter()`
         * `do_action()` and `apply_filters()` know only the alias name and not the original object event name.
         */
    }

    public function testAggregateProvider(): void
    {
        $aggregateProvider = new AggregateProvider();
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
        $aggregateProvider->addProvider($listenerProvider);
        $dispatcher = new \ItalyStrap\Event\Dispatcher($aggregateProvider);

        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        $listener = function (object $event) {
            $event->rendered = 'Hello there';
        };

        $listenerProvider->addListener(EventForRenderer::class, $listener);

        $dispatcher->dispatch($event);
        Assert::assertSame('Hello there', $event->rendered);
    }

    public function testTryToCreateBridgeBetweenEventNameAndPSR14(): void
    {
        $title = \apply_filters('the_title', 'Hello World');

        Assert::assertSame('Hello World', $title);

        $theTitleEvent = new class ('Hello World') {
            public string $title;

            public function __construct(string $title)
            {
                $this->title = $title;
            }

            public function __toString()
            {
                return \apply_filters('the_title', $this->title, \get_the_ID());
            }
        };

        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $listener = function (object $event) {
            $event->title = 'Hello Universe';
        };

        $listenerProvider->addListener(\get_class($theTitleEvent), $listener);

        $title = (string)$dispatcher->dispatch($theTitleEvent);

        Assert::assertSame('Hello Universe', $title);

        $customTitleEvent = new class ('Hello World') {
            public const EVENT_NAME = 'custom_title';

            public string $title;

            public function __construct(string $title)
            {
                $this->title = $title;
            }

            public function __toString()
            {
                return $this->title;
            }
        };

        $titleEvent = (object)\apply_filters($customTitleEvent::EVENT_NAME, $customTitleEvent);

        Assert::assertSame('Hello World', $titleEvent->title);
    }

    public function testApplyFiltersAndDoActionApproachWithObjectEvent(): void
    {
        $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
        $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider);

        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        /**
         * If we use object for the classic Hook WordPress API and we want to use
         * in particular the `apply_filters()` remember that the listener
         * must return the value.
         */
        $listener = function (object $event): object {
            $event->rendered = 'Hello there';
            return $event;
        };

        $listenerProvider->addListener(EventForRenderer::class, $listener);

        $dispatcher->dispatch($event);
        Assert::assertSame('Hello there', $event->rendered);

        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        \do_action(EventForRenderer::class, $event);
        Assert::assertSame('Hello there', $event->rendered);

        $event = new EventForRenderer();
        Assert::assertSame('Hello World', $event->rendered);

        $event = (object)\apply_filters(EventForRenderer::class, $event);
        Assert::assertSame('Hello there', $event->rendered);
    }
}
