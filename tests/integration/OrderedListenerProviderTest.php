<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\OrderedListenerProvider;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\OrderedListenerProviderTestTrait;

class OrderedListenerProviderTest extends IntegrationTestCase
{
    use OrderedListenerProviderTestTrait;
    private function makeInstance(): OrderedListenerProvider
    {
        return new OrderedListenerProvider();
    }
}
