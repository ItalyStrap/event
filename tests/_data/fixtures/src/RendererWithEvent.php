<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

class RendererWithEvent
{
    private \ItalyStrap\Event\Dispatcher $dispatcher;

    public function __construct(\ItalyStrap\Event\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function render(): string
    {
        return $this->dispatcher->dispatch(new EventForRenderer())->render();
    }
}
