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
final class SubscribersConfigExtension implements Extension
{
    /** @var string */
    public const SUBSCRIBERS = 'subscribers';

    private SubscriberRegister $subscriberRegister;

    /** @var ConfigInterface<array-key, mixed> */
    private ConfigInterface $config;

    /**
     * EventResolverExtension constructor.
     *
     * @param SubscriberRegister $subscriberRegister
     * @param ConfigInterface<array-key, mixed> $config
     */
    public function __construct(SubscriberRegister $subscriberRegister, ConfigInterface $config)
    {
        $this->subscriberRegister = $subscriberRegister;
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
        $application->walk($this->name(), $this);
    }

    /**
     * @param string $class Array value from yous configuration
     * @param int|string $index_or_optionName Array key from your configuration
     * @param Injector $injector An instance of the Injector::class
     * @throws ConfigException
     * @throws InjectionException
     */
    public function __invoke(string $class, $index_or_optionName, Injector $injector): void
    {

        if (\is_string($index_or_optionName) && empty($this->config->get($index_or_optionName, false))) {
            return;
        }

        /** @var SubscriberInterface $subscriber */
        $subscriber = $injector->share($class)->make($class);
        $this->subscriberRegister->addSubscriber($subscriber);
    }
}
