<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\EventSubscription;
use ItalyStrap\Event\SubscriberInterface;

trait EventSubscriptionTrait
{
    /**
     * @var callable $callback
     */
    private $callback = null;
    private int $priority = 10;
    private int $acceptedArgs = 1;

    public function testShouldBeInstantiable()
    {
        $sut = $this->makeInstance();
        $this->assertInstanceOf(EventSubscription::class, $sut);
    }

    public function testItShouldReturnCorrectArrayWithCorrectKeyValuesEqualsToConstructorArguments()
    {
        $this->callback = function () {
        };
        $this->priority = 20;
        $this->acceptedArgs = 2;

        $sut = $this->makeInstance();
        $this->assertIsArray($sut->toArray());
        $this->assertArrayHasKey(SubscriberInterface::CALLBACK, $sut->toArray());
        $this->assertArrayHasKey(SubscriberInterface::PRIORITY, $sut->toArray());
        $this->assertArrayHasKey('accepted_args', $sut->toArray());
        $this->assertSame($this->callback, $sut->toArray()[SubscriberInterface::CALLBACK]);
        $this->assertSame($this->priority, $sut->toArray()[SubscriberInterface::PRIORITY]);
        $this->assertSame($this->acceptedArgs, $sut->toArray()[SubscriberInterface::ACCEPTED_ARGS]);
    }
}
