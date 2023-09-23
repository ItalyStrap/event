<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use function add_filter;
use function current_filter;
use function has_filter;
use function remove_all_filters;
use function remove_filter;

trait LegacyEventDispatcherMethodsDeprecatedTrait
{
    private string $deprecationPattern = 'This method %1$s::%2$s() is deprecated, use %3$s() instead.';

    public function addListener(
        string $eventName,
        callable $listener,
        int $priority = 10,
        int $accepted_args = 3
    ): bool {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\ItalyStrap\Event\GlobalOrderedListenerProvider::' . __FUNCTION__
        );

        return add_filter($eventName, $listener, $priority, $accepted_args);
    }

    public function removeListener(
        string $eventName,
        callable $listener,
        int $priority = 10
    ): bool {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\ItalyStrap\Event\GlobalOrderedListenerProvider::' . __FUNCTION__
        );

        return remove_filter($eventName, $listener, $priority);
    }

    public function removeAllListener(string $eventName, $priority = false): bool
    {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\ItalyStrap\Event\GlobalOrderedListenerProvider::' . __FUNCTION__
        );

        return remove_all_filters($eventName, $priority);
    }

    public function hasListener(string $eventName, $callback = false)
    {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\ItalyStrap\Event\GlobalOrderedListenerProvider::' . __FUNCTION__
        );

        return has_filter($eventName, $callback);
    }

    /**
     * @param string $event_name
     * @param mixed ...$args
     * @return object
     * @infection-ignore-all
     * @psalm-suppress PossiblyUnusedParam
     */
    public function dispatch($event_name, ...$args): object
    {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\Psr\EventDispatcher\EventDispatcherInterface::' . __FUNCTION__
        );

        $this->trigger($event_name, ...$args);

        if (isset($args[0]) && \is_object($args[0])) {
            return $args[0];
        }

        return new class {
        };
    }

    /**
     * @deprecated
     * @infection-ignore-all
     * @param mixed ...$args
     * @psalm-suppress PossiblyUnusedParam
     */
    public function execute(string $event_name, ...$args): void
    {
        $pattern = <<<'D_MESSAGE'
This method %1$s::%2$s() is deprecated, use %1$s::%3$s() instead.
D_MESSAGE;

        $this->deprecated($pattern, __FUNCTION__, 'trigger');

        $this->trigger($event_name, ...$args);
    }

    public function currentEventName(): string
    {

        $this->deprecated(
            $this->deprecationPattern,
            __FUNCTION__,
            '\ItalyStrap\Event\GlobalState::' . __FUNCTION__
        );

        return current_filter();
    }

    /**
     * @psalm-suppress UnusedMethod
     */
    private function deprecated(string $pattern, string $oldMethodName, string $newMethodName): void
    {
        \trigger_error(
            \sprintf(
                $pattern,
                self::class,
                $oldMethodName,
                $newMethodName
            ),
            \E_USER_DEPRECATED
        );
    }
}
