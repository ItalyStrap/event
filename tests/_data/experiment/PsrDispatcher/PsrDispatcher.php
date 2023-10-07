<?php

declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\ListenerRegisterInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrDispatcherInterface;

// https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
class PsrDispatcher implements PsrDispatcherInterface
{
    /**
     * @var array
     */
    private $wp_filter;

    private \ItalyStrap\PsrDispatcher\CallableFactoryInterface $factory;

    private \ItalyStrap\Event\EventDispatcherInterface $dispatcher;
    private ListenerRegisterInterface $listenerRegister;

    public function __construct(
        array &$wp_filter,
        CallableFactoryInterface $factory,
        ListenerRegisterInterface $listenerRegister,
        EventDispatcherInterface $dispatcher
    ) {
        $this->wp_filter = &$wp_filter;
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
        $this->listenerRegister = $listenerRegister;
    }

    /**
     * @inheritDoc
     */
    public function addListener(
        string $event_name,
        callable $listener,
        int $priority = 10,
        int $accepted_args = 1
    ): bool {
        /** @var callable $callback */
        $callback = $this->factory->buildCallable($listener);
        return $this->listenerRegister->addListener($event_name, $callback, $priority, $accepted_args);
    }

    /**
     * @inheritDoc
     */
    public function removeListener(
        string $event_name,
        callable $listener,
        int $priority = 10
    ): bool {

        if (! isset($this->wp_filter[ $event_name ][ $priority ])) {
            return false;
        }

        foreach ((array) $this->wp_filter[ $event_name ][ $priority ] as $method_name_registered => $value) {
            if (! $value['function'] instanceof ListenerHolderInterface) {
                throw new \RuntimeException(\sprintf(
                    'The callable is not an instance of %s',
                    ListenerHolderInterface::class
                ));
            }

            if ($value['function']->listener() === $listener) {
                $value['function']->nullListener();
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event): object
    {
        $this->dispatcher->trigger(\get_class($event), $event);
        return $event;
    }
}
