<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * Warns the developers who still listen to event names no longer used, with the notice of WordPress.
 *
 * A filter and an action are stored the same way, so `do_action_deprecated()` warns both: it runs
 * their listeners with the arguments of the event and ignores what a filter returns.
 *
 * @psalm-api
 */
final class DeprecatedEventsSubscriber implements SubscriberInterface
{
    private const DEFAULT_EVENT = 'init';

    /**
     * @var iterable<DeprecatedEvent>
     */
    private iterable $events;

    private string $eventName;

    /**
     * @param iterable<DeprecatedEvent> $events The event names no longer used.
     * @param string $eventName The event that triggers the warnings, after the listeners are added.
     */
    public function __construct(iterable $events, string $eventName = self::DEFAULT_EVENT)
    {
        $this->events = $events;
        $this->eventName = $eventName;
    }

    public function getSubscribedEvents(): iterable
    {
        yield $this->eventName => 'warn';
    }

    public function warn(): void
    {
        foreach ($this->events as $event) {
            \do_action_deprecated(
                $event->name(),
                $event->args(),
                $event->version(),
                $event->replacement(),
                $event->message()
            );
        }
    }
}
