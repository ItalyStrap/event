<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @psalm-api
 */
final class GlobalOrderedListenerProvider implements ListenerProviderInterface, ListenerRegisterInterface
{
    public function addListener(
        string $eventName,
        callable $listener,
        int $priority = 10,
        int $accepted_args = 3
    ): bool {
        return \add_filter($eventName, $listener, $priority, $accepted_args);
    }

    public function removeListener(
        string $eventName,
        callable $listener,
        int $priority = 10
    ): bool {
        return \remove_filter($eventName, $listener, $priority);
    }

    public function removeAllListener(string $eventName, $priority = false): bool
    {
        return \remove_all_filters($eventName, $priority);
    }

    public function hasListener(string $eventName, $callback = false)
    {
        return \has_filter($eventName, $callback);
    }

    public function getListenersForEvent(object $event): iterable
    {
        /** @psalm-var array $wp_filter */
        global $wp_filter;
        $callbacks = [];
        $eventName = \get_class($event);

        if (!\array_key_exists($eventName, $wp_filter)) {
            return $callbacks;
        }

        if (!$wp_filter[$eventName] instanceof \WP_Hook) {
            return $callbacks;
        }

        /**
         * \WP_Hook::callbacks is a multidimensional array
         * with priority as the first dimension and
         * the callback name as the second dimension.
         * Example:
         * [
         *     10 => [ // $priority
         *        'callback1' => [
         *           'function' => 'callback1', // 'callback1' is the name of the function or $idx
         *           'accepted_args' => 1,
         *        ],
         *        'callback2' => [
         *            'function' => 'callback2',
         *            'accepted_args' => 1,
         *        ],
         *     ],
         * ]
         * @var array<int, array<string, array{function: callable, accepted_args: int}>> $callbacks
         */
        foreach ($wp_filter[$eventName]->callbacks as $callbacks) {
            foreach ($callbacks as $callback) {
                yield $callback['function'];
            }
        }
    }
}
