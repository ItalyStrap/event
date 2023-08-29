<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

use function get_class;

/**
 * @author Larry Garfield https://github.com/Crell/Tukio
 * @psalm-api
 */
class DebugDispatcher implements EventDispatcherInterface
{
    public const M_DEBUG = 'Processing event of type {type}.';

    protected EventDispatcherInterface $dispatcher;

    protected LoggerInterface $logger;

    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function dispatch(object $event): object
    {
        $this->logger->debug(self::M_DEBUG, ['type' => get_class($event), 'event' => $event]);
        return $this->dispatcher->dispatch($event);
    }
}
