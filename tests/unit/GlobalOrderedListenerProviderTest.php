<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Tests\OrderedListenerProviderTestTrait;
use ItalyStrap\Tests\UnitTestCase;
use PHPUnit\Framework\Assert;
use tad\FunctionMockerLe;

class GlobalOrderedListenerProviderTest extends UnitTestCase
{
    use OrderedListenerProviderTestTrait;

    private function makeInstance(): GlobalOrderedListenerProvider
    {
        return new GlobalOrderedListenerProvider();
    }

    public function testItShouldAddListener()
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

    public function testItShouldRemoveListener()
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

    public function testItShouldHasListener()
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

    public function testItShouldRemoveAllListener()
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
