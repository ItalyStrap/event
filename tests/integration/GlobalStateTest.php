<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\GlobalState;
use ItalyStrap\Tests\GlobalStateTestTrait;
use ItalyStrap\Tests\IntegrationTestCase;
use PHPUnit\Framework\Assert;
use Psr\EventDispatcher\EventDispatcherInterface;

class GlobalStateTest extends IntegrationTestCase
{
    use GlobalStateTestTrait;

    public function makeDispatcher(): EventDispatcherInterface
    {
        return $this->createMock(EventDispatcherInterface::class);
    }

    public function makeInstance(): GlobalState
    {
        return new GlobalState();
    }
}
