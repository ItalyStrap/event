<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\SubscriberMock;
use ItalyStrap\Tests\SubscriberRegisterTestTrait;

class SubscriberRegisterTest extends IntegrationTestCase
{
    use SubscriberRegisterTestTrait;

    private function makeInstance(): SubscriberRegister
    {
        return new SubscriberRegister(
            new GlobalOrderedListenerProvider()
        );
    }

    /**
     * @dataProvider subscriberProvider()
     */
    public function testItShouldAddSubscriberWith($provider_args): void
    {
        $sut = $this->makeInstance();
        $subscriber = new SubscriberMock($provider_args);
        $sut->addSubscriber($subscriber);
    }

    /**
     * @dataProvider subscriberProvider()
     */
    public function testItShouldRemoveSubscriberWith($provider_args): void
    {
        $sut = $this->makeInstance();
        $subscriber = new SubscriberMock($provider_args);
        $sut->removeSubscriber($subscriber);
    }
}
