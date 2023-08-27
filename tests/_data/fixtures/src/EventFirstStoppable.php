<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Psr\EventDispatcher\StoppableEventInterface;

class EventFirstStoppable implements StoppableEventInterface
{
    public $value = 0;

    protected $propagationStopped = false;

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
