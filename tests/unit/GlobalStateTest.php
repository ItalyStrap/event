<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\GlobalState;
use ItalyStrap\Tests\GlobalStateTestTrait;
use ItalyStrap\Tests\UnitTestCase;

class GlobalStateTest extends UnitTestCase
{
//    use GlobalStateTestTrait;

    public function makeInstance(): GlobalState
    {
        return new GlobalState();
    }
}
