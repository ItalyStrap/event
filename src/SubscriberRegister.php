<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use ItalyStrap\Event\SubscriberInterface as Subscriber;
use RuntimeException;

use function gettype;
use function is_array;
use function is_callable;
use function is_iterable;
use function is_string;
use function method_exists;
use function sprintf;

/**
 * @psalm-api
 */
class SubscriberRegister implements SubscriberRegisterInterface
{
    private const ACCEPTED_ARGS = 1;
    private const PRIORITY = 10;

    private ListenerRegisterInterface $listenerRegister;

    public function __construct(ListenerRegisterInterface $listenerRegister)
    {
        $this->listenerRegister = $listenerRegister;
    }

    public function addSubscriber(Subscriber $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $event_name => $parameters) {
            if (is_array($parameters) && isset($parameters[0]) && is_iterable($parameters[0])) {
                foreach ($parameters as $listener) {
                    $this->addSubscriberListener($subscriber, $event_name, $listener);
                }
                continue;
            }

            $this->addSubscriberListener($subscriber, $event_name, $parameters);
        }
    }

    /**
     * Adds the given subscriber listener to the list of event listeners
     * that listen to the given event.
     *
     * @param Subscriber $subscriber
     * @param string $event_name
     * @param mixed $parameters
     */
    private function addSubscriberListener(Subscriber $subscriber, string $event_name, $parameters): void
    {
        $this->listenerRegister->addListener(
            $event_name,
            $this->buildCallable($subscriber, $parameters),
            ...$this->buildParameters($parameters)
        );
    }

    public function removeSubscriber(Subscriber $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $event_name => $parameters) {
            if (is_array($parameters) && isset($parameters[0]) && is_iterable($parameters[0])) {
                foreach ($parameters as $listener) {
                    $this->removeSubscriberListener($subscriber, $event_name, $listener);
                }
                continue;
            }
            $this->removeSubscriberListener($subscriber, $event_name, $parameters);
        }
    }

    /**
     * Adds the given subscriber listener to the list of event listeners
     * that listen to the given event.
     *
     * @param Subscriber $subscriber
     * @param string $event_name
     * @param mixed $parameters
     */
    private function removeSubscriberListener(Subscriber $subscriber, string $event_name, $parameters): void
    {
        $this->listenerRegister->removeListener(
            $event_name,
            $this->buildCallable($subscriber, $parameters),
            ...$this->buildParameters($parameters)
        );
    }

    /**
     * @param Subscriber $subscriber
     * @param mixed $parameters
     * @return callable
     */
    private function buildCallable(Subscriber $subscriber, $parameters): callable
    {
        if (is_callable($parameters)) {
            return $parameters;
        }

        if (is_string($parameters) && method_exists($subscriber, $parameters)) {
            return [$subscriber, $parameters];
        }

        if (
            isset($parameters[Subscriber::CALLBACK])
            && is_callable($parameters[Subscriber::CALLBACK])
        ) {
            return $parameters[Subscriber::CALLBACK];
        }

        if (
            isset($parameters[Subscriber::CALLBACK])
            && method_exists($subscriber, (string)$parameters[Subscriber::CALLBACK])
        ) {
            return [$subscriber, $parameters[Subscriber::CALLBACK]];
        }

        throw new RuntimeException(sprintf(
            'Impossible to build a valid callable because $parameters is a type of %s',
            gettype($parameters)
        ));
    }

    /**
     * @param mixed $parameters
     * @return array<int, int>
     */
    private function buildParameters($parameters): array
    {
        if (is_callable($parameters) || !is_array($parameters)) {
            return [
                self::PRIORITY,
                self::ACCEPTED_ARGS,
            ];
        }

        return [
            (int)($parameters[Subscriber::PRIORITY] ?? self::PRIORITY),
            (int)($parameters[Subscriber::ACCEPTED_ARGS] ?? self::ACCEPTED_ARGS),
        ];
    }
}
