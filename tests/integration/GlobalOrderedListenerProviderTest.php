<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\OrderedListenerProviderTestTrait;

class GlobalOrderedListenerProviderTest extends IntegrationTestCase
{
    use OrderedListenerProviderTestTrait;

    private function makeInstance(): GlobalOrderedListenerProvider
    {
        return new GlobalOrderedListenerProvider();
    }
}
