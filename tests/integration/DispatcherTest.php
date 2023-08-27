<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\Dispatcher;
use ItalyStrap\Event\NullListenerProvider;
use ItalyStrap\Event\OrderedListenerProvider;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class DispatcherTest extends IntegrationTestCase
{
    private function makeDispatcher(ListenerProviderInterface $listenerProvider): EventDispatcherInterface
    {
        return new Dispatcher($listenerProvider);
    }

    private function makeListenerProvider(): ListenerProviderInterface
    {
        return new OrderedListenerProvider();
    }

    public function testItShouldDoNothingWithNullProvider()
    {
        $sut = $this->makeDispatcher(
            new NullListenerProvider()
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
        $this->assertSame($event, $actual, 'The event should be the same');
        $this->assertFalse($isCalled, 'The event should not be called');

        \do_action(\stdClass::class, $event);
        $this->assertTrue(\property_exists($event, 'value'), 'The event should have the property value');
        $this->assertTrue($isCalled, 'The event should not be called');
    }

    public function testItShouldDoingEvent()
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

    public function testItShouldDispatchEvent()
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

        $this->assertSame(42, $event->value, 'The event value should be 42');
        $this->assertSame(84, $event->newValue, 'The event value should be 84');
        $this->assertTrue((int)\did_action(\stdClass::class) > 0, 'The action should be called');
        $this->assertTrue(\has_action(\stdClass::class), 'The action should be registered');
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

        $this->assertSame(42, $actual->value, 'The event value should be 42');
    }
}
