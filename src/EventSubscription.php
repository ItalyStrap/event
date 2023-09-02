<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
class EventSubscription
{
    private array $eventSubscriber;

    /**
     * @param callable $callback
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function __construct(
        $callback,
        int $priority = 10,
        int $acceptedArgs = 3
    ) {
        $this->eventSubscriber = [
            SubscriberInterface::CALLBACK      => $callback,
            SubscriberInterface::PRIORITY      => $priority,
            SubscriberInterface::ACCEPTED_ARGS => $acceptedArgs,
        ];
    }

    public function toArray(): array
    {
        return $this->eventSubscriber;
    }
}
