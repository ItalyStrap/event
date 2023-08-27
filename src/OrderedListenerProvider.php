<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @psalm-api
 */
class OrderedListenerProvider implements ListenerProviderInterface
{
    public function addListener(string $eventName, callable $listener, int $priority = 10): void
    {
        \add_filter(
            $eventName,
            $listener,
            $priority,
        );
    }

    public function removeListener(string $eventName, callable $listener, int $priority = 10): void
    {
        \remove_filter(
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
}
