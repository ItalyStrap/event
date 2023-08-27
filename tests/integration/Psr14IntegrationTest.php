<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\PsrDispatcher\PsrDispatcher;
use ItalyStrap\PsrDispatcher\CallableFactory;
use ItalyStrap\Tests\EventFirst;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\ListenerChangeValueToText;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Psr14IntegrationTest extends IntegrationTestCase
{
    private function makeDispatcher(): PsrDispatcher
    {
        global $wp_filter;
        return new PsrDispatcher($wp_filter, new CallableFactory(), new \ItalyStrap\Event\EventDispatcher());
    }

    /**
     * @test
     */
    public function itShouldAddFunctionListenerAndChangeValue()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');

        /** @var object $event */
        $event = $sut->dispatch(new EventFirst());

        $this->assertEquals(42, $event->value, '');
        $this->assertFalse($event->isPropagationStopped(), '');
    }

    /**
     * @test
     */
    public function itShouldRemoveFunctionListenerAndReturnValueWithoutChanges()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->removeListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');

        /** @var object $event */
        $event = $sut->dispatch(new EventFirst());

        $this->assertEquals(0, $event->value, '');
        $this->assertFalse($event->isPropagationStopped(), '');
    }

    /**
     * @test
     */
    public function itShouldStopPropagation()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->addListener(EventFirst::class, [new ListenerChangeValueToText(), 'changeText' ]);

        // Here it will set value to false and stop propagation
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation');
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_77');


        $event = new EventFirst();

        /** @var object $event */
        $sut->dispatch($event);

        $this->assertEquals(false, $event->value, '');
        $this->assertTrue($event->isPropagationStopped(), '');
    }

    /**
     * @test
     */
    public function itShouldNotStopPropagation()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->addListener(EventFirst::class, [new ListenerChangeValueToText(), 'changeText' ]);
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_77');


        $event = new EventFirst();

        /** @var object $event */
        $sut->dispatch($event);

        $this->assertEquals(77, $event->value, '');
        $this->assertFalse($event->isPropagationStopped(), '');
    }

    /**
     * @test
     */
    public function itShouldRemoveListenerAndReturnValue77()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->addListener(EventFirst::class, [new ListenerChangeValueToText(), 'changeText' ]);
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation');
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_77');

        $sut->removeListener(
            EventFirst::class,
            'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation'
        );

        $event = new EventFirst();

        /** @var object $event */
        $sut->dispatch($event);

        $this->assertEquals(77, $event->value, '');
        $this->assertFalse($event->isPropagationStopped(), '');
    }

    /**
     * @test
     */
    public function ifSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagation()
    {

        $sut = $this->makeDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->addListener(EventFirst::class, [new ListenerChangeValueToText(), 'changeText' ]);
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation');
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_77');

        $event = new EventFirst();

        /** @var object $event */
        $event = $sut->dispatch($event);

        $this->assertEquals(false, $event->value, '');
        $this->assertTrue($event->isPropagationStopped(), '');

        $event = $sut->dispatch(new EventFirst());

        $this->assertEquals(false, $event->value, '');
        $this->assertTrue($event->isPropagationStopped(), '');
    }

    /**
     *
     */
    public function ifSameEventIsDispatchedMoreThanOnceItShouldStopPropagationIfListenerStopPropagationSymfonyMirror()
    {
        $sut = new EventDispatcher();

        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_42');
        $sut->addListener(EventFirst::class, [new ListenerChangeValueToText(), 'changeText' ]);
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_false_and_stop_propagation');
        $sut->addListener(EventFirst::class, 'ItalyStrap\Tests\listener_change_value_to_77');

        $event = new EventFirst();

        /** @var object $event */
        $event = $sut->dispatch($event);

        $this->assertEquals(false, $event->value, '');
        $this->assertTrue($event->isPropagationStopped(), '');

        $event = $sut->dispatch(new EventFirst());

        $this->assertEquals(false, $event->value, '');
        $this->assertTrue($event->isPropagationStopped(), '');
    }

    public function testServerRequest()
    {
        codecept_debug($_SERVER['REQUEST_TIME']);
        codecept_debug(\json_encode(\is_int($_SERVER['REQUEST_TIME']), JSON_THROW_ON_ERROR));
    }
}