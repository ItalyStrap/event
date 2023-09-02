<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Tests\SubscriberMock;
use ItalyStrap\Tests\SubscriberRegisterTestTrait;
use ItalyStrap\Tests\UnitTestCase;
use Prophecy\Argument;

class SubscriberRegisterTest extends UnitTestCase
{
    use SubscriberRegisterTestTrait;

    private function makeInstance(): SubscriberRegister
    {
        return new SubscriberRegister($this->makeHooks());
    }

    /**
     * @dataProvider subscriberProvider()
     */
    public function testItShouldAddSubscriberWith($provider_args): void
    {
        $test = $this;
        $sut = $this->makeInstance();

        $this->subscriberMock
            ->executeCallable()
            ->willReturn(true);

        $this->subscriberMock
            ->getSubscribedEvents()
            ->willReturn($provider_args);

        $this->hooks->addListener(
            Argument::type('string'),
            Argument::type('callable'),
            Argument::type('int'),
            Argument::type('int')
        )->willReturn(true)->shouldBeCalled();

        $sut->addSubscriber($this->makeSubscriberMock());
    }

    /**
     * @dataProvider subscriberProvider()
     */
    public function testItShouldRemoveSubscriberWith($provider_args): void
    {
        $test = $this;
        $sut = $this->makeInstance();
        $subscriber = new SubscriberMock($provider_args);

        $this->subscriberMock
            ->executeCallable()
            ->willReturn(true);

        $this->subscriberMock
            ->getSubscribedEvents()
            ->willReturn($provider_args);

        $this->hooks->removeListener(
            Argument::type('string'),
            Argument::type('callable'),
            Argument::type('int'),
            Argument::type('int')
        )->willReturn(true)->shouldBeCalled();

        $sut->removeSubscriber($this->makeSubscriberMock());
    }

    public function testItShouldThrownIfParameterOfSubscriberIsNotValid(): void
    {
        $test = $this;
        $sut = $this->makeInstance();

        $this->subscriber->getSubscribedEvents()->willReturn([
            'event_name'            => [new \stdClass()],
        ]);

        $this->hooks->addListener()->shouldNotBeCalled();

        $this->expectException(\RuntimeException::class);
        $sut->addSubscriber($this->makeSubscriber());
    }
}
