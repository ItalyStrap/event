<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Empress\AurynResolverInterface;
use ItalyStrap\Empress\Extension;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\Tests\SubscriberMock;
use Prophecy\Argument;

class SubscribersConfigExtensionTest extends UnitTestCase
{
    protected function makeInstance(): SubscribersConfigExtension
    {
        $sut = new SubscribersConfigExtension($this->makeSubscriberRegister(), $this->makeConfig());
        $this->assertInstanceOf(Extension::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldHaveName()
    {
        $sut = $this->makeInstance();
        $this->assertStringContainsString(SubscribersConfigExtension::SUBSCRIBERS, $sut->name(), '');
    }

    /**
     * @test
     */
    public function callbackShouldSubscribeListenersWithIndexedArray()
    {
        $subscriber = $this->prophesize(SubscriberMock::class);

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldBeCalled();
        $this->config->get()->shouldNotBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->makeFakeInjector())
            ->shouldBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldBeCalled();

        $sut = $this->makeInstance();
        $sut(SubscriberMock::class, 0, $this->makeFakeInjector());
    }

    /**
     * @test
     */
    public function callbackShouldSubscribeListenersFormAssociativeArrayWithTrueOptionKey()
    {
        $subscriber = $this->prophesize(SubscriberMock::class);
        $config = [
            'key'   => true
        ];
        $key = \array_keys($config)[0];

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldBeCalled();
        $this->config->get($key, false)->willReturn($config[$key])->shouldBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->makeFakeInjector())
            ->shouldBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldBeCalled();

        $sut = $this->makeInstance();
        $sut(SubscriberMock::class, $key, $this->makeFakeInjector());
    }

    /**
     * @test
     */
    public function callbackShouldNotSubscribeListenersFromAssociativeArrayWithFalseOptionKey()
    {
        $subscriber = $this->prophesize(SubscriberMock::class);
        $config = [
            'key'   => false
        ];
        $key = \array_keys($config)[0];

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldNotBeCalled();
        $this->config->get($key, false)->willReturn($config[$key])->shouldBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->makeFakeInjector())
            ->shouldNotBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldNotBeCalled();

        $sut = $this->makeInstance();
        $sut(SubscriberMock::class, $key, $this->makeFakeInjector());
    }

    /**
     * @test
     */
    public function itShouldExecute()
    {
        $application = $this->prophesize(AurynResolverInterface::class);

        $application->walk(Argument::type('string'), Argument::type('callable'))->shouldBeCalled();

        $sut = $this->makeInstance();
        $sut->execute($application->reveal());
    }
}
