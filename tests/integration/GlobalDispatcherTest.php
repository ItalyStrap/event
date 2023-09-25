<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Tests\ClassWithDispatchDependency;
use ItalyStrap\Tests\IntegrationTestCase;

class GlobalDispatcherTest extends IntegrationTestCase
{
    private function makeDispatcher(): EventDispatcher
    {
        return new EventDispatcher();
    }

    public function testItShouldOutputTextOnEventName()
    {
        $sut = $this->makeDispatcher();
        $listenerRegister = new GlobalOrderedListenerProvider();

        $listenerRegister->addListener('event_name', function () {
            echo 'Value printed';
        });

        $this->expectOutputString('Value printed');
        $sut->trigger('event_name');
    }

    public function testClassWithDispatchDependency()
    {
        $sut = $this->makeDispatcher();
        $listenerRegister = new GlobalOrderedListenerProvider();

        $some_class = new ClassWithDispatchDependency($sut);

        $listenerRegister->addListener(
            ClassWithDispatchDependency::EVENT_NAME,
            fn(string $value) => 'New value'
        );

        $some_class->filterValue();

        $this->assertStringContainsString('New value', $some_class->value(), '');
    }
}
