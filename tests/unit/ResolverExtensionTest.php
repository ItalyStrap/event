<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Empress\AurynResolverInterface;
use ItalyStrap\Empress\Extension;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\Tests\Subscriber;
use Prophecy\Argument;

class ResolverExtensionTest extends UnitTestCase
{
    protected function makeInstance(): SubscribersConfigExtension
    {
        $sut = new SubscribersConfigExtension($this->getSubscriberRegister(), $this->getConfig());
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
        $subscriber = $this->prophesize(Subscriber::class);

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldBeCalled();
        $this->config->get()->shouldNotBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->getFakeInjector())
            ->shouldBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldBeCalled();

        $sut = $this->makeInstance();
        $sut->walk(Subscriber::class, 0, $this->getFakeInjector());
    }

    /**
     * @test
     */
    public function callbackShouldSubscribeListenersFormAssociativeArrayWithTrueOptionKey()
    {
        $subscriber = $this->prophesize(Subscriber::class);
        $config = [
            'key'   => true
        ];
        $key = \array_keys($config)[0];

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldBeCalled();
        $this->config->get($key, false)->willReturn($config[$key])->shouldBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->getFakeInjector())
            ->shouldBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldBeCalled();

        $sut = $this->makeInstance();
        $sut->walk(Subscriber::class, $key, $this->getFakeInjector());
    }

    /**
     * @test
     */
    public function callbackShouldNotSubscribeListenersFromAssociativeArrayWithFalseOptionKey()
    {
        $subscriber = $this->prophesize(Subscriber::class);
        $config = [
            'key'   => false
        ];
        $key = \array_keys($config)[0];

        $this->subscriberRegister->addSubscriber($subscriber->reveal())->shouldNotBeCalled();
        $this->config->get($key, false)->willReturn($config[$key])->shouldBeCalled();

        $this->fake_injector->share(Argument::type('string'))
            ->willReturn($this->getFakeInjector())
            ->shouldNotBeCalled();

        $this->fake_injector->make(Argument::type('string'))
            ->willReturn($subscriber->reveal())
            ->shouldNotBeCalled();

        $sut = $this->makeInstance();
        $sut->walk(Subscriber::class, $key, $this->getFakeInjector());
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
