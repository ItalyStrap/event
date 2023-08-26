<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Config\Config;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\PsrDispatcher\CallableFactory;
use ItalyStrap\PsrDispatcher\CallableFactoryInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Psr\Log\LoggerInterface;
use UnitTester;
use tad\FunctionMockerLe;

class UnitTestCase extends Unit
{
    use ProphecyTrait;

    protected UnitTester $tester;

    protected ObjectProphecy $hooks;

    protected function getHooks(): EventDispatcherInterface
    {
        return $this->hooks->reveal();
    }

    protected ObjectProphecy $subscriber;

    protected function getSubscriber(): SubscriberInterface
    {
        return $this->subscriber->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $config;

    protected function getConfig(): Config
    {
        return $this->config->reveal();
    }


    protected \Prophecy\Prophecy\ObjectProphecy $fake_injector;

    protected function getFakeInjector(): Injector
    {
        return $this->fake_injector->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $subscriberRegister;

    protected function getSubscriberRegister(): SubscriberRegister
    {
        return $this->subscriberRegister->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $dispatcher;

    protected function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $psrDispatcher;

    protected function getPsrDispatcher(): PsrEventDispatcherInterface
    {
        return $this->psrDispatcher->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $factory;

    /**
     * @return CallableFactoryInterface
     */
    protected function getFactory(): CallableFactoryInterface
    {
        return $this->factory->reveal();
    }

    protected \Prophecy\Prophecy\ObjectProphecy $logger;
    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger->reveal();
    }


    // phpcs:ignore -- Method from Codeception
    protected function _before() {
        $this->hooks = $this->prophesize(EventDispatcherInterface::class);
        $this->dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->subscriber = $this->prophesize(SubscriberInterface::class);

        $this->fake_injector = $this->prophesize(Injector::class);
        $this->subscriberRegister = $this->prophesize(SubscriberRegister::class);
        $this->config = $this->prophesize(Config::class);

        $this->factory = $this->prophesize(CallableFactory::class);
        $this->psrDispatcher = $this->prophesize(PsrEventDispatcherInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        global $wp_filter;
        $wp_filter = [];
    }

    // phpcs:ignore -- Method from Codeception
    protected function _after()
    {
        FunctionMockerLe\undefineAll([
            'do_action',
            'add_filter',
            'remove_filter',
            'apply_filters',
            'current_filter',
            'has_filter',
            'remove_all_filters'
        ]);

        global $wp_filter;
        $wp_filter = [];
    }
}
