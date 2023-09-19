<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Tests\UnitTestCase;
use PHPUnit\Framework\Assert;
use tad\FunctionMockerLe;

class EventDispatcherTest extends UnitTestCase
{
    public function makeInstance(): EventDispatcher
    {
        $sut = new EventDispatcher();
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut);
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldAddListener()
    {
        $sut = $this->makeInstance();

        $args = [
            'event',
            function () {
            },
            10,
            3,
        ];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('add_filter', function () use (&$calls, $args) {
            $calls++;
            Assert::assertEquals($args, func_get_args());
            return true;
        });

        $sut->addListener(...$args);

        $this->assertEquals(1, $calls);
    }

    /**
     * @test
     */
    public function itShouldRemoveListener()
    {
        $sut = $this->makeInstance();

        $args = [
            'event',
            function () {
            },
            10,
            3,
        ];

        $calls = 0;

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define(
            'remove_filter',
            function ($hook_name, $callback, $priority = 10) use (&$calls) {
                $calls++;
                return true;
            }
        );

        $sut->removeListener(...$args);

        $this->assertEquals(1, $calls);
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

    /**
     * @test
     */
    public function itShouldReturnCurrentHook()
    {
        $sut = $this->makeInstance();

        $hook_name = 'hook_name';

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('current_filter', function () use (&$calls, $hook_name) {
            $calls++;
            return $hook_name;
        });

        $this->assertEquals($hook_name, $sut->currentEventName(), '');
        $this->assertEquals(1, $calls);
    }

    /**
     * @test
     */
    public function itShouldHasListener()
    {
        $sut = $this->makeInstance();

        $return_val = true;

        $args = [
            'event',
            function () {
            },
        ];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('has_filter', function () use (&$calls, $return_val, $args) {
            $calls++;
            Assert::assertEquals($args, func_get_args());
            return $return_val;
        });

        $this->assertEquals($return_val, $sut->hasListener(...$args), '');
        $this->assertEquals(1, $calls);
    }

    /**
     * @test
     */
    public function itShouldRemoveAllListener()
    {
        $sut = $this->makeInstance();

        $return_val = true;

        $args = [
            'event',
            function () {
            },
        ];

		// phpcs:ignore -- Method from Codeception
		FunctionMockerLe\define('remove_all_filters', function ( string $event_name ) use (&$calls, $return_val, $args): bool {
            $calls++;
            Assert::assertEquals($args[0], $event_name);
            return $return_val;
        });

        $this->assertEquals($return_val, $sut->removeAllListener($args[0]), '');
        $this->assertTrue($sut->removeAllListener($args[0]), '');
        $this->assertEquals(2, $calls);
    }
}
