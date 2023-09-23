<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Config\Config;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\ListenerRegisterInterface;
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

    protected function makeHooks(): EventDispatcherInterface
    {
        return $this->hooks->reveal();
    }

    protected ObjectProphecy $subscriber;

    protected function makeSubscriber(): SubscriberInterface
    {
        return $this->subscriber->reveal();
    }

    protected ObjectProphecy $subscriberMock;

    protected function makeSubscriberMock(): SubscriberMock
    {
        return $this->subscriberMock->reveal();
    }

    protected ObjectProphecy $config;

    protected function makeConfig(): Config
    {
        return $this->config->reveal();
    }


    protected ObjectProphecy $fake_injector;

    protected function makeFakeInjector(): Injector
    {
        return $this->fake_injector->reveal();
    }

    protected ObjectProphecy $subscriberRegister;

    protected function makeSubscriberRegister(): SubscriberRegister
    {
        return $this->subscriberRegister->reveal();
    }

    protected ObjectProphecy $globalDispatcher;

    protected function makeDispatcher(): EventDispatcherInterface
    {
        return $this->globalDispatcher->reveal();
    }

    protected ObjectProphecy $listenerRegister;

    protected function makeListenerRegister(): ListenerRegisterInterface
    {
        return $this->listenerRegister->reveal();
    }

    protected ObjectProphecy $psrDispatcher;

    protected function makePsrDispatcher(): PsrEventDispatcherInterface
    {
        return $this->psrDispatcher->reveal();
    }

    protected ObjectProphecy $factory;

    /**
     * @return CallableFactoryInterface
     */
    protected function makeFactory(): CallableFactoryInterface
    {
        return $this->factory->reveal();
    }

    protected ObjectProphecy $logger;
    /**
     * @return LoggerInterface
     */
    protected function makeLogger(): LoggerInterface
    {
        return $this->logger->reveal();
    }

    // phpcs:ignore -- Method from Codeception
    protected function _before() {
        $this->hooks = $this->prophesize(EventDispatcherInterface::class);
        $this->globalDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->listenerRegister = $this->prophesize(ListenerRegisterInterface::class);
        $this->subscriber = $this->prophesize(SubscriberInterface::class);
        $this->subscriberMock = $this->prophesize(SubscriberMock::class);

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
