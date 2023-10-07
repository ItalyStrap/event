<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Dispatcher;
use ItalyStrap\Event\GlobalState;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DispatcherTest extends IntegrationTestCase
{
    private function makeDispatcher(ListenerProviderInterface $listenerProvider): EventDispatcherInterface
    {
        return new Dispatcher(
            $listenerProvider,
            new GlobalState()
        );
    }

    private function makeListenerProvider(): ListenerProviderInterface
    {
        return new GlobalOrderedListenerProvider();
    }

    public function testItShouldDoNothingWithNullProvider()
    {
        $sut = $this->makeDispatcher(
            new class implements ListenerProviderInterface {
                public function getListenersForEvent(object $event): iterable
                {
                    return [];
                }
            }
        );

        $event = new \stdClass();

        $isCalled = false;
        \add_filter(
            \stdClass::class,
            function (object $event) use (&$isCalled) {
                $event->value = 42;
                $isCalled = true;
            }
        );

        $actual = $sut->dispatch($event);
        Assert::assertSame($event, $actual, 'The event should be the same');
        Assert::assertFalse($isCalled, 'The event should not be called');

        \do_action(\stdClass::class, $event);
        Assert::assertTrue(\property_exists($event, 'value'), 'The event should have the property value');
        Assert::assertTrue($isCalled, 'The event should not be called');
    }

    public function testItShouldDispatchEvent()
    {
        $provider = $this->makeListenerProvider();

        $isCalled = false;
        $provider->addListener(
            \stdClass::class,
            function (object $event) use (&$isCalled) {
                Assert::assertTrue(\doing_filter(\current_filter()));
                $isCalled = true;
            }
        );

        $sut = $this->makeDispatcher(
            $provider
        );

        $event = new \stdClass();
        $sut->dispatch($event);
        Assert::assertTrue($isCalled, 'The event should be called');
    }

    public function testCurrentEventNameItShouldMatchTheEventObject()
    {
        $provider = $this->makeListenerProvider();

        $provider->addListener(
            \stdClass::class,
            function (object $event) {
                Assert::assertSame(\stdClass::class, \current_filter());
                Assert::assertSame(\get_class($event), \current_filter());
                $event->value = 42;
            }
        );

        $sut = $this->makeDispatcher(
            $provider
        );

        $event = new \stdClass();
        $sut->dispatch($event);
        Assert::assertTrue(\property_exists($event, 'value'), 'The event should have the property value');
    }

    public function testItShouldDispatchEvent2()
    {
        $provider = $this->makeListenerProvider();

        $provider->addListener(
            \stdClass::class,
            function (object $event) {
                $event->value = 42;
            }
        );

        $provider->addListener(
            \stdClass::class,
            function (object $event) {
                $event->value = 42 ** 2;
            },
            9
        );

        \add_filter(
            \stdClass::class,
            function (object $event) {
                $event->newValue = 84;
            }
        );

        $event = new \stdClass();

        $sut = $this->makeDispatcher(
            $provider
        );

        $actual = $sut->dispatch($event);

        Assert::assertSame(42, $event->value, 'The event value should be 42');
        Assert::assertSame(84, $event->newValue, 'The event value should be 84');
        Assert::assertTrue((int)\did_action(\stdClass::class) > 0, 'The action should be called');
        Assert::assertTrue(\has_action(\stdClass::class), 'The action should be registered');
    }

    public function testItShouldStopPropagation()
    {
        $provider = $this->makeListenerProvider();

        $event = new EventMayStopPropagation();

        $eventName = \get_class($event);

        $provider->addListener(
            $eventName,
            function (object $event) {
                $event->value = 42;
                \assert(\method_exists($event, 'stopPropagation')) and $event->stopPropagation();
            }
        );

        $provider->addListener(
            $eventName,
            function (object $event) {
                $event->value = 42 ** 2;
            }
        );

        $dispatcher = $this->makeDispatcher(
            $provider
        );

        $actual = $dispatcher->dispatch($event);

        Assert::assertSame(42, $actual->value, 'The event value should be 42');
    }

    public function testItShouldAddFunctionListenerAndChangeValue()
    {

        $provider = $this->makeListenerProvider();

        $sut = $this->makeDispatcher(
            $provider
        );

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );

        $event = $sut->dispatch(new EventFirstStoppable());

        Assert::assertEquals(42, $event->value, '');
        Assert::assertFalse($event->isPropagationStopped(), 'It should not stop propagation');
    }

    public function testItShouldRemoveFunctionListenerAndReturnValueWithoutChanges()
    {

        $provider = $this->makeListenerProvider();

        $sut = $this->makeDispatcher(new GlobalOrderedListenerProvider());

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $provider->removeListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );

        /** @var object $event */
        $event = $sut->dispatch(new EventFirstStoppable());

        Assert::assertEquals(0, $event->value, '');
        Assert::assertFalse($event->isPropagationStopped(), 'It should not stop propagation');
    }

    public function testItShouldStopPropagationWithMoreListener()
    {

        $provider = new GlobalOrderedListenerProvider();

        $sut = $this->makeDispatcher($provider);

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            [new ListenerChangeValueToText(), 'changeText' ]
        );

        // Here it will set value to false and stop propagation
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_77'
        );


        $event = new EventFirstStoppable();

        /** @var object $event */
        $sut->dispatch($event);

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');
    }

    public function testItShouldRemoveListenerAndReturnValue77()
    {
        $provider = new GlobalOrderedListenerProvider();

        $sut = $this->makeDispatcher($provider);

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            [new ListenerChangeValueToText(), 'changeText' ]
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_77'
        );

        $provider->removeListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );

        $event = new EventFirstStoppable();

        /** @var object $event */
        $sut->dispatch($event);

        Assert::assertEquals(77, $event->value, '');
        Assert::assertFalse($event->isPropagationStopped(), '');
    }

    public function testIfSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagation()
    {
        $provider = new GlobalOrderedListenerProvider();

        $sut = $this->makeDispatcher($provider);

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            [new ListenerChangeValueToText(), 'changeText' ]
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_77'
        );

        $event = new EventFirstStoppable();

        /** @var object $event */
        $event = $sut->dispatch($event);

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');

        $event = $sut->dispatch(new EventFirstStoppable());

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');
    }

    public function testIfSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagationWithSymfony()
    {
        $sut = new EventDispatcher();

        $sut->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $sut->addListener(
            EventFirstStoppable::class,
            [new ListenerChangeValueToText(), 'changeText' ]
        );
        $sut->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );
        $sut->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_77'
        );

        $event = new EventFirstStoppable();

        /** @var object $event */
        $event = $sut->dispatch($event);

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');

        $event = $sut->dispatch(new EventFirstStoppable());

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');
    }

    public function testCallDispatchTwoTimesWithSameEvent()
    {
        $provider = new GlobalOrderedListenerProvider();

        $sut = $this->makeDispatcher($provider);

        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_42'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            [new ListenerChangeValueToText(), 'changeText' ]
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );
        $provider->addListener(
            EventFirstStoppable::class,
            'ItalyStrap\Tests\listener_change_value_to_77'
        );

        $event = new EventFirstStoppable();

        /** @var object $event */
        $event = $sut->dispatch($event);

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');

        $event = $sut->dispatch($event);

        Assert::assertEquals(false, $event->value, '');
        Assert::assertTrue($event->isPropagationStopped(), '');
    }
}
