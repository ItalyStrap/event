<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\SubscriberInterface;

class SubscriberMock implements SubscriberInterface
{
    private iterable $provider_args;

    public function __construct(iterable $provider_args)
    {
        $this->provider_args = $provider_args;
    }

    public function executeCallable(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): iterable
    {
        return $this->provider_args;
    }
}
