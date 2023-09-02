<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\GlobalState;
use ItalyStrap\Tests\GlobalStateTestTrait;
use ItalyStrap\Tests\IntegrationTestCase;
use PHPUnit\Framework\Assert;

class GlobalStateTest extends IntegrationTestCase
{
    use GlobalStateTestTrait;

    public function makeInstance(): GlobalState
    {
        return new GlobalState();
    }
}
