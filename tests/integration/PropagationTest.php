<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Integration;

use ItalyStrap\Event\PropagationAwareTrait;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\PropagationTestTrait;

class PropagationTest extends IntegrationTestCase
{
    use PropagationTestTrait;

    public function makeInstance(): object
    {
        return new class {
            use PropagationAwareTrait;
        };
    }
}
