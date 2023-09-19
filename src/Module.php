<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @psalm-api
 */
final class Module
{
    public function __invoke(): array
    {
        return [
            'aliases' => [
                // Global
                EventDispatcherInterface::class => EventDispatcher::class,
                SubscriberRegisterInterface::class => SubscriberRegister::class,
                // PSR-14
                \Psr\EventDispatcher\EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => GlobalOrderedListenerProvider::class,
                ListenerRegisterInterface::class => GlobalOrderedListenerProvider::class,
                StateInterface::class => GlobalState::class,
            ],
            'sharing' => [
                EventDispatcher::class,
                SubscriberRegister::class,
                // PSR-14
                Dispatcher::class,
                GlobalOrderedListenerProvider::class,
                GlobalState::class,
            ],
        ];
    }
}
