<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\SubscriberInterface;
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
        return new SubscriberRegister($this->makeListenerRegister());
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

        $this->listenerRegister->addListener(
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

        $this->listenerRegister->removeListener(
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

        $this->listenerRegister->addListener()->shouldNotBeCalled();

        $this->expectException(\RuntimeException::class);
        $sut->addSubscriber($this->makeSubscriber());
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function globalFunctionNamesProvider(): iterable
    {
        yield 'method name' => ['link'];
        yield 'method name as callback' => [
            [SubscriberInterface::CALLBACK => 'link', SubscriberInterface::PRIORITY => 99],
        ];
    }

    /**
     * @dataProvider globalFunctionNamesProvider()
     */
    public function testItShouldPreferTheSubscriberMethodOverAGlobalFunction($parameters): void
    {
        $subscriber = new class ($parameters) implements SubscriberInterface {
            /** @var mixed */
            private $parameters;

            /**
             * @param mixed $parameters
             */
            public function __construct($parameters)
            {
                $this->parameters = $parameters;
            }

            public function getSubscribedEvents(): iterable
            {
                yield 'wp_footer' => $this->parameters;
            }

            public function link(): void
            {
            }
        };

        $this->listenerRegister->addListener(
            'wp_footer',
            [$subscriber, 'link'],
            Argument::type('int'),
            Argument::type('int')
        )->willReturn(true)->shouldBeCalled();

        $this->makeInstance()->addSubscriber($subscriber);
    }
}
