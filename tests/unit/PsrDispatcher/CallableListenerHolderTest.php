<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\PsrDispatcher;

use Fig\EventDispatcher\ParameterDeriverTrait;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\PsrDispatcher\CallableListenerHolder;
use ItalyStrap\PsrDispatcher\ListenerHolderInterface;
use ItalyStrap\Tests\ListenerChangeValueToText;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\StoppableEventInterface;
use stdClass;

class CallableListenerHolderTest extends UnitTestCase
{
    use ParameterDeriverTrait;

    private function makeInstance(callable $listener): CallableListenerHolder
    {
        $sut = new CallableListenerHolder($listener);
        $this->assertInstanceOf(ListenerHolderInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $sut = $this->makeInstance(static function (object $event) {
        });
    }

    /**
     * @test
     */
    public function itShouldReturnListener()
    {
        $listener = static function (object $event) {
        };
        $sut = $this->makeInstance($listener);
        $this->assertSame($listener, $sut->listener(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnVoidListener()
    {
        $event = new \stdClass();
        $event->value = 0;

        $listener = static function (object $event) {
            $event->value = 42;
        };
        $sut = $this->makeInstance($listener);
        $sut->nullListener();
        $sut($event);

        $this->assertEmpty($event->value, '');
    }

    /**
     * @test
     */
    public function itShouldExecute()
    {
        $event = $this->prophesize(StoppableEventInterface::class);

        $event->isPropagationStopped()->willReturn(false);

        $calls = 0;
        $listener = static function (object $event_obj) use ($event, &$calls) {
            Assert::assertSame($event->reveal(), $event_obj, '');
            $calls++;
        };

        $sut = $this->makeInstance($listener);
        $sut($event->reveal());

        $this->assertTrue(1 === $calls, '');
    }

    /**
     * @test
     */
    public function itShouldNotExecuteIfEventIsStopped()
    {
        $event = $this->prophesize(StoppableEventInterface::class);

        $event->isPropagationStopped()->willReturn(true);

        $calls = 0;
        $listener = static function (object $event_obj) use ($event, &$calls) {
            // Never called
            $calls++;
        };

        $sut = $this->makeInstance($listener);
        $sut($event->reveal());

        $this->assertTrue(0 === $calls, '');
    }

    /**
     * @test
     */
    public function testSomeCallable()
    {
        $event = new stdClass();

        $listener = new ListenerChangeValueToText();
        $sut = $this->makeInstance([ $listener, 'changeText' ]);

        $sut($event);

        $this->assertTrue('new value' === $event->value, '');
    }

    /**
     * @test
     */
//    public function testSomeFeature()
//    {
//      $sut = $this->getInstance( static function ( object $event ) {} );
//      codecept_debug( $type = $this->getParameterType( function ( object $event ) {} ) );
//      $this->assertTrue( $type === 'object' || ( new \ReflectionClass( $type ) )->isInstantiable() );
//    }
}
