<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
interface StateInterface
{
    public const BEFORE = 'before';
    public const AFTER = 'after';

    public function forEvent(object $event): void;

    public function progress(string $state): void;

    public function currentEventName(): string;

    public function isDispatching(): bool;

    public function dispatchedEventCount(): int;
}
