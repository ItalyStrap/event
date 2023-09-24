<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Unit;

use ItalyStrap\Event\PropagationAwareTrait;
use ItalyStrap\Tests\PropagationTestTrait;
use ItalyStrap\Tests\UnitTestCase;

class PropagationTest extends UnitTestCase
{
    use PropagationTestTrait;

    public function makeInstance(): object
    {
        return new class {
            use PropagationAwareTrait;
        };
    }
}
