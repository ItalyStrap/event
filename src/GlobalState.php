<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

/**
 * @psalm-api
 */
final class GlobalState implements StateInterface
{
    private string $eventName = '';

    public function forEvent(object $event, \Psr\EventDispatcher\EventDispatcherInterface $provider): void
    {
        $this->eventName = \get_class($event);
        global $wp_current_filter;
        $wp_current_filter[] = $this->eventName;
    }

    public function progress(string $state, \Psr\EventDispatcher\EventDispatcherInterface $provider): void
    {
        switch ($state) {
            case self::BEFORE:
                $this->incrDispatchedEvent();
                break;
            case self::AFTER:
                $this->dispatchedEvent();
                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'The state "%s" is not valid',
                    $state
                ));
        }
    }

    private function incrDispatchedEvent(): void
    {
        global $wp_actions, $wp_filters;
        /** @var array<string, int> $wp_actions*/
        $wp_actions[$this->eventName] = ($wp_actions[$this->eventName] ?? 0) + 1;
        /** @var array<string, int> $wp_filters*/
        $wp_filters[$this->eventName] = ($wp_filters[$this->eventName] ?? 0) + 1;
    }

    private function dispatchedEvent(): void
    {
        global $wp_current_filter;
        \array_pop($wp_current_filter);
    }

    /**
     * Suppressing this error because if for any reason the global $wp_current_filter is empty
     * this method will return false instead of an empty string.
     * @psalm-suppress RedundantCastGivenDocblockType
     */
    public function currentEventName(): string
    {
        return (string)\current_filter();
    }

    public function dispatchedEventCount(): int
    {
        // In this case I call did_action() instead of did_filter() because in unit test the function was not defined.
        return \did_action($this->eventName);
    }

    public function isDispatching(): bool
    {
        return \doing_filter($this->eventName);
    }

    public function __debugInfo()
    {
        return [
            'eventName' => $this->eventName,
            'currentEvent' => $this->currentEventName(),
            'dispatchingEvent' => $this->isDispatching(),
            'dispatchedEventCount' => $this->dispatchedEventCount(),
        ];
    }
}
