<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use function apply_filters;
use function do_action;

/**
 * @psalm-api
 */
class GlobalDispatcher implements EventDispatcherInterface, ListenerRegisterInterface
{
    use LegacyEventDispatcherMethodsDeprecatedTrait;

    public function trigger(string $event_name, ...$args): void
    {
        do_action($event_name, ...$args);
    }

    public function filter(string $event_name, $value, ...$args)
    {
        return apply_filters($event_name, $value, ...$args);
    }
}
