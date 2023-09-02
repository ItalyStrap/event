<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use function add_filter;
use function apply_filters;
use function current_filter;
use function do_action;
use function has_filter;
use function remove_all_filters;
use function remove_filter;

/**
 * @psalm-api
 */
class EventDispatcher implements EventDispatcherInterface
{
    protected const ACCEPTED_ARGS = 3;
    protected const PRIORITY = 10;

    public function addListener(
        string $eventName,
        callable $listener,
        int $priority = self::PRIORITY,
        int $accepted_args = self::ACCEPTED_ARGS
    ): bool {
        return add_filter($eventName, $listener, $priority, $accepted_args);
    }

    public function removeListener(
        string $eventName,
        callable $listener,
        int $priority = self::PRIORITY
    ): bool {
        return remove_filter($eventName, $listener, $priority);
    }

    public function removeAllListener(string $event_name, $priority = false): bool
    {
        return remove_all_filters($event_name, $priority);
    }

    /**
     * @infection-ignore-all
     * @psalm-suppress MissingReturnType
     */
    public function dispatch($event_name, ...$args): object
    {
        // phpcs:disable
        $pattern = <<<'D_MESSAGE'
Arguments for %1$s::%2$s() are deprecated, in a future release this method will be removed or it will be make compliant with %3$s::%4$s() so only one argument as object type will be accepted.
D_MESSAGE; //phpcs:enable

        @\trigger_error(
            \sprintf(
                $pattern,
                self::class,
                __FUNCTION__,
                \Psr\EventDispatcher\EventDispatcherInterface::class,
                'dispatch'
            ),
            \E_USER_DEPRECATED
        );

        do_action($event_name, ...$args);

        if (isset($args[0]) && \is_object($args[0])) {
            return $args[0];
        }

         return new class {
         };
    }

    /**
     * @param mixed ...$args
     * @deprecated
     * @infection-ignore-all
     */
    public function execute(string $event_name, ...$args): void
    {
        $pattern = <<<'D_MESSAGE'
This method %1$s::%2$s() is deprecated, use %1$s::%3$s() instead.
D_MESSAGE;

        @\trigger_error(
            \sprintf(
                $pattern,
                self::class,
                __FUNCTION__,
                'action'
            ),
            \E_USER_DEPRECATED
        );

        $this->action($event_name, ...$args);
    }

    public function action(string $event_name, ...$args): void
    {
        do_action($event_name, ...$args);
    }

    public function filter(string $event_name, $value, ...$args)
    {
        return apply_filters($event_name, $value, ...$args);
    }

    public function currentEventName()
    {
        return current_filter();
    }

    public function hasListener(string $event_name, $callback = false)
    {
        return has_filter($event_name, $callback);
    }
}
