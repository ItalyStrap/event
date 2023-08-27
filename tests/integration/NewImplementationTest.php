<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class NewImplementationTest extends IntegrationTestCase
{
    private function makeDispatcher(ListenerProviderInterface $listenerProvider): EventDispatcherInterface
    {
        return new class ($listenerProvider) implements EventDispatcherInterface {
            private ListenerProviderInterface $listenerProvider;

            public function __construct(ListenerProviderInterface $listenerProvider)
            {
                $this->listenerProvider = $listenerProvider;
            }

            public function dispatch(object $event): object
            {
                $eventName = \get_class($event);
                global $wp_current_filter, $wp_actions, $wp_filters;

                $wp_current_filter[] = $eventName;
                $wp_actions[$eventName] = ($wp_actions[$eventName] ?? 0) + 1;
                $wp_filters[$eventName] = ($wp_filters[$eventName] ?? 0) + 1;

                foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
                    if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                        \remove_all_filters(\current_filter());
                        break;
                    }
                    $listener($event);
                }

                \array_pop($wp_current_filter);

                return $event;
            }
        };
    }

    private function makeListenerProvider(): ListenerProviderInterface
    {
        return new class ((array)$GLOBALS['wp_filter']) implements ListenerProviderInterface {
            private array $eventCollection;

            public function __construct(array $eventCollection)
            {
                $this->eventCollection = $eventCollection;
            }

            public function addListener(string $eventName, callable $listener, int $priority = 10): void
            {
                \add_filter(
                    $eventName,
                    $listener,
                    $priority,
                );
            }

            public function getListenersForEvent(object $event): iterable
            {
                global $wp_filter;
                $callbacks = [];
                $eventName = \get_class($event);

                if (!\array_key_exists($eventName, $wp_filter)) {
                    return $callbacks;
                }

                if (!$wp_filter[$eventName] instanceof \WP_Hook) {
                    return $callbacks;
                }

                foreach ($wp_filter[$eventName]->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $idx => $callback) {
                        yield $callback['function'];
                    }
                }
            }
        };
    }

    public function testItShouldDispatchEvent()
    {
        $provider = $this->makeListenerProvider();

        $provider->addListener(
            \stdClass::class,
            function (object $event) {
                codecept_debug('Event name one inside addListener: ' . \get_class($event));
                codecept_debug(\current_filter());
                codecept_debug(\doing_filter(\current_filter()));
                $event->value = 42;
            }
        );

        $provider->addListener(
            \stdClass::class,
            function (object $event) {
                Assert::assertSame(\stdClass::class, \current_filter());
                $event->value = 42 ** 2;
            },
            9
        );


        \add_filter(
            \stdClass::class,
            function (object $event) {
                codecept_debug('Event name inside add_filter: ' . \get_class($event));
                codecept_debug('Current filter: ' . \current_filter());
                $event->newValue = 84;
            }
        );

        $event = new \stdClass();

        $dispatcher = $this->makeDispatcher(
            $provider
        );

        $actual = $dispatcher->dispatch($event);

        $this->assertSame(42, $event->value, 'The event value should be 42');
        $this->assertSame(84, $event->newValue, 'The event value should be 84');
        $this->assertTrue((int)\did_action(\stdClass::class) > 0, 'The action should be called');
        $this->assertTrue(\has_action(\stdClass::class), 'The action should be registered');
    }

    public function testItShouldStopPropagation()
    {
        $provider = $this->makeListenerProvider();

        $event = new class extends \stdClass implements StoppableEventInterface {
            public bool $propagationStopped = false;
            public int $value = 0;

            public function isPropagationStopped(): bool
            {
                return $this->propagationStopped;
            }

            public function stopPropagation(): void
            {
                $this->propagationStopped = true;
            }
        };

        $eventName = \get_class($event);

        $provider->addListener(
            $eventName,
            function (object $event) {
                $event->value = 42;
                \assert($event instanceof StoppableEventInterface) and $event->stopPropagation();
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
