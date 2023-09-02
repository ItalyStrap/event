<?php

declare(strict_types=1);

namespace ItalyStrap\Event;

use Auryn\ConfigException;
use Auryn\InjectionException;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Empress\AurynConfigInterface;
use ItalyStrap\Empress\Extension;
use ItalyStrap\Empress\Injector;

/**
 * @psalm-api
 */
class SubscribersConfigExtension implements Extension
{
    /** @var string */
    public const SUBSCRIBERS = 'subscribers';

    private \ItalyStrap\Event\SubscriberRegister $event_manager;

    private \ItalyStrap\Config\ConfigInterface $config;

    /**
     * EventResolverExtension constructor.
     * @param SubscriberRegister $event_manager
     * @param ConfigInterface $config
     */
    public function __construct(SubscriberRegister $event_manager, ConfigInterface $config)
    {
        $this->event_manager = $event_manager;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return self::SUBSCRIBERS;
    }

    /**
     * @inheritDoc
     */
    public function execute(AurynConfigInterface $application): void
    {
        $application->walk($this->name(), [$this, 'walk']);
    }

    /**
     * @param string $class Array value from yous configuration
     * @param int|string $index_or_optionName Array key from your configuration
     * @param Injector $injector An instance of the Injector::class
     * @throws ConfigException
     * @throws InjectionException
     */
    public function walk(string $class, $index_or_optionName, Injector $injector): void
    {

        if (\is_string($index_or_optionName) && empty($this->config->get($index_or_optionName, false))) {
            return;
        }

        /** @var SubscriberInterface $subscriber */
        $subscriber = $injector->share($class)->make($class);
        $this->event_manager->addSubscriber($subscriber);
    }
}
