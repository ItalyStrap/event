<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

class NullState implements StateInterface
{
    public function forEvent(object $event): void
    {
    }

    public function progress(string $state): void
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
