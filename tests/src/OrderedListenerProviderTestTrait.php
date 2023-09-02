<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

trait OrderedListenerProviderTestTrait
{
    public function testListenersForEventShouldBeEmptyByDefault(): void
    {
        $sut = $this->makeInstance();
        $listenerForEvent = $sut->getListenersForEvent(new \stdClass());
        $this->assertEmpty(\iterator_to_array($listenerForEvent));
    }
}