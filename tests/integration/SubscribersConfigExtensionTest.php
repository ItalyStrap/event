<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\SubscriberMock;

class SubscribersConfigExtensionTest extends IntegrationTestCase
{
    public function testItShouldConfigureSubscribersExtension()
    {

        $injector = new Injector();
        $injector->share($injector);

        $injector->alias(EventDispatcherInterface::class, EventDispatcher::class);
        $injector->share(EventDispatcherInterface::class);
        $injector->share(SubscriberRegister::class);
        $injector->defineParam('provider_args', [
            'event'  => function () {
                echo 'Some text';
            },
        ]);

        $event_resolver = $injector->make(SubscribersConfigExtension::class, [
            ':config'   => ConfigFactory::make([
                SubscriberMock::class   => false
            ]),
        ]);

        $dependencies = ConfigFactory::make([
            SubscribersConfigExtension::SUBSCRIBERS => [
                SubscriberMock::class,
            ],
        ]);

        $empress = $injector->make(AurynResolver::class, [
            ':dependencies' => $dependencies
        ]);
        $empress->extend($event_resolver);
        $empress->resolve();

        $this->expectOutputString('Some text');
        ( $injector->make(EventDispatcher::class) )->trigger('event');
    }
}
