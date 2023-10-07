<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
final class EventSubscription
{
    /** @var array{'function_to_add':callable, 'priority':int, 'accepted_args':int} */
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

    /** @return array{'function_to_add':callable, 'priority':int, 'accepted_args':int} */
    public function toArray(): array
    {
        return $this->eventSubscriber;
    }

    /** @return array{'function_to_add':callable, 'priority':int, 'accepted_args':int} */
    public function __invoke(): array
    {
        return $this->eventSubscriber;
    }
}
