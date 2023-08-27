<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\{ListenerProviderInterface, StoppableEventInterface};

/**
 * @psalm-api
 */
class Dispatcher implements \Psr\EventDispatcher\EventDispatcherInterface
{
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
                break;
            }
            $listener($event);
        }

        \array_pop($wp_current_filter);

        return $event;
    }
}
