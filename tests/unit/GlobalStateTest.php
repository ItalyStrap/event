<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\GlobalState;
use ItalyStrap\Tests\GlobalStateTestTrait;
use ItalyStrap\Tests\UnitTestCase;
use tad\FunctionMockerLe;

class GlobalStateTest extends UnitTestCase
{
//    use GlobalStateTestTrait;

    public function makeInstance(): GlobalState
    {
        return new GlobalState();
    }

    public function testItShouldReturnCurrentHook()
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
}
