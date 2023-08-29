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
         * the callback as the second dimension.
         * Example:
         * [
         *     10 => [ // priority
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
