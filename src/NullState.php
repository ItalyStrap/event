<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

final class NullState implements StateInterface
{
    public function forEvent(object $event, \Psr\EventDispatcher\EventDispatcherInterface $provider): void
    {
    }

    public function progress(string $state, \Psr\EventDispatcher\EventDispatcherInterface $provider): void
    {
    }

    public function currentEventName(): string
    {
        return '';
    }

    public function isDispatching(): bool
    {
        return false;
    }

    public function dispatchedEventCount(): int
    {
        return 0;
    }
}
