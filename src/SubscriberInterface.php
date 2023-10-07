<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
interface SubscriberInterface
{
    public const CALLBACK      = 'function_to_add';
    public const PRIORITY      = 'priority';
    public const ACCEPTED_ARGS = 'accepted_args';

    /**
     * Returns an array of hooks that this subscriber wants to register with
     * the WordPress plugin API.
     *
     * @return iterable<string, int|string|object|callable|array{
     *         callback: callable,
     *         priority?: int,
     *         accepted_args?: int
     *     }|array{
     *         0: object,
     *         1: string,
     *     }|list<array{
     *         callback: callable,
     *         priority?: int,
     *         accepted_args?: int
     *     }>>
     */
    public function getSubscribedEvents(): iterable;
}
