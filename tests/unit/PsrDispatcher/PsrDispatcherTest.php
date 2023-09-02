<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\PsrDispatcher;

use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\PsrDispatcher\PsrDispatcher;
use ItalyStrap\PsrDispatcher\ListenerHolderInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use stdClass;

use function uniqid;

class PsrDispatcherTest extends UnitTestCase
{
    public function makeInstance(): PsrDispatcher
    {
        global $wp_filter;
        $sut = new PsrDispatcher($wp_filter, $this->makeFactory(), $this->makeDispatcher());
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldDispatch()
    {
        $event = new stdClass();
        $expected = [
            'event_name'    => get_class($event),
            'event'         => $event,
        ];

        $this->dispatcher
            ->dispatch(
                Argument::type('string'),
                Argument::type('object')
            )
            ->will(function ($args) use ($expected) {
                Assert::assertSame($expected['event_name'], $args[0]);
                Assert::assertSame($expected['event'], $args[1]);
            })
            ->shouldBeCalled();

        $sut = $this->makeInstance();
        $result = $sut->dispatch($event);
        $this->assertSame($event, $result, 'It should return the same event');
    }

    /**
     * @test
     */
    public function itShouldAddListener()
    {
        $eventObj = new stdClass();
        $eventName = get_class($eventObj);

        $sut = $this->makeInstance();

        $this->factory
            ->buildCallable(Argument::type('callable'))
            ->will(fn($args) => static fn() => $eventObj)
            ->shouldBeCalled();

        $this->dispatcher
            ->addListener(
                Argument::type('string'),
                Argument::type('callable'),
                Argument::type('integer'),
                Argument::type('integer')
            )
            ->will(function ($args) use ($eventName): bool {
                Assert::assertSame($eventName, $args[0], '');
                return true;
            })
            ->shouldBeCalled();

        $sut->addListener($eventName, static function (object $event) {
            //No called here
        });
    }

    /**
     * @test
     */
    public function itShouldRemoveListener()
    {

        global $wp_filter;
        $eventObj = new stdClass();
        $eventName = get_class($eventObj);

        $listener = static function (object $event) {
            //No called here
        };

        $listener_holder = $this->prophesize(ListenerHolderInterface::class);
        $listener_holder->listener()->willReturn($listener)->shouldBeCalled();
        $listener_holder->nullListener()->shouldBeCalled();

        $wp_filter[$eventName][10][ uniqid()]['function'] = $listener_holder->reveal();

        $sut = $this->makeInstance();

        $sut->removeListener($eventName, $listener);
    }

    /**
     * @test
     */
    public function itShouldReturnBeforeRemoveListener()
    {

        global $wp_filter;
        $eventObj = new stdClass();
        $eventName = get_class($eventObj);

        $listener = static function (object $event) {
            //No called here
        };

        $listener_holder = $this->prophesize(ListenerHolderInterface::class);
        $listener_holder->listener()->shouldNotBeCalled();

        $wp_filter[$eventName][10] = null;

        $sut = $this->makeInstance();

        $this->assertFalse($sut->removeListener($eventName, $listener), '');
    }

    /**
     * @test
     */
    public function itShouldThrownErrorOnRemoveListenerIfIsNotListenerHolderInterface()
    {

        global $wp_filter;
        $eventObj = new stdClass();
        $eventName = get_class($eventObj);

        $listener = static function (object $event) {
            //No called here
        };

        $listener_holder = $this->prophesize(stdClass::class);

        $wp_filter[$eventName][10][ uniqid()]['function'] = [
            $listener_holder->reveal(),
            'execute'
        ];

        $sut = $this->makeInstance();

        $this->expectException(RuntimeException::class);
        $sut->removeListener($eventName, $listener);
    }
}
