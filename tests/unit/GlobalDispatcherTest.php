<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Tests\UnitTestCase;
use PHPUnit\Framework\Assert;
use tad\FunctionMockerLe;

class GlobalDispatcherTest extends UnitTestCase
{
    public function makeInstance(): EventDispatcher
    {
        $sut = new EventDispatcher();
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut);
        return $sut;
    }

    public function argumentsProvider(): iterable
    {
        return [
            '2 params passed'   => [
                [
                    'event_name',
                    'arg 1',
                ]
            ],
            '3 params passed'   => [
                [
                    'event_name',
                    'arg 1',
                    'arg 2',
                ]
            ],
            '4 params passed'   => [
                [
                    'event_name',
                    'arg 1',
                    'arg 2',
                    'arg 3',
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider argumentsProvider()
     */
    public function itShouldExecuteWith($args)
    {
        $sut = $this->makeInstance();

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('do_action', function () use (&$calls, $args) {
            $calls++;
            Assert::assertEquals($args, func_get_args());
        });

        $sut->trigger(...$args);

        $this->assertEquals(1, $calls);
    }

    /**
     * @test
     * @dataProvider argumentsProvider()
     */
    public function itShouldFiltersWith($args)
    {
        $sut = $this->makeInstance();

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('apply_filters', function () use (&$calls, $args) {
            $calls++;
            Assert::assertEquals($args, func_get_args());
        });

        $sut->filter(...$args);

        $this->assertEquals(1, $calls);
    }
}
