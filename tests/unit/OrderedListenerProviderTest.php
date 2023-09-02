<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\OrderedListenerProvider;
use ItalyStrap\Tests\OrderedListenerProviderTestTrait;
use ItalyStrap\Tests\UnitTestCase;

class OrderedListenerProviderTest extends UnitTestCase
{
    use OrderedListenerProviderTestTrait;
    private function makeInstance(): OrderedListenerProvider
    {
        return new OrderedListenerProvider();
    }

}
