<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
final class EventSubscription
{
    private array $eventSubscriber;

    /**
     * @param callable $callback
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function __construct(
        callable $callback,
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

    public function __invoke(): array
    {
        return $this->eventSubscriber;
    }
}
