<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Tests\OrderedListenerProviderTestTrait;
use ItalyStrap\Tests\UnitTestCase;

class GlobalOrderedListenerProviderTest extends UnitTestCase
{
    use OrderedListenerProviderTestTrait;

    private function makeInstance(): GlobalOrderedListenerProvider
    {
        return new GlobalOrderedListenerProvider();
    }
}
