<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

class EventMayStopPropagation implements \Psr\EventDispatcher\StoppableEventInterface
{
    public bool $propagationStopped = false;
    public int $value = 0;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
