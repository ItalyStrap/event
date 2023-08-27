<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

class NullListenerProvider implements ListenerProviderInterface
{
    public function getListenersForEvent(object $event): iterable
    {
        return [];
    }
}
