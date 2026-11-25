<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Event\DeprecatedEvent;
use ItalyStrap\Event\DeprecatedEventsSubscriber;
use ItalyStrap\Tests\IntegrationTestCase;

class DeprecatedEventsSubscriberTest extends IntegrationTestCase
{
    public function testItListensToInitByDefault(): void
    {
        $sut = new DeprecatedEventsSubscriber([]);

        $this->assertSame(['init' => 'warn'], \iterator_to_array($sut->getSubscribedEvents()));
    }

    public function testItListensToTheGivenEvent(): void
    {
        $sut = new DeprecatedEventsSubscriber([], 'plugins_loaded');

        $this->assertSame(['plugins_loaded' => 'warn'], \iterator_to_array($sut->getSubscribedEvents()));
    }

    public function testItWarnsTheListenersOfADeprecatedEvent(): void
    {
        $this->setExpectedDeprecated('old_filter');
        $received = [];
        \add_filter('old_filter', static function (array $value) use (&$received): array {
            $received = $value;
            return $value;
        });

        $sut = new DeprecatedEventsSubscriber([
            new DeprecatedEvent('old_filter', '3.0.0', 'new_filter', 'Use the new filter.', [['a']]),
            new DeprecatedEvent('old_action', '3.0.0'),
        ]);
        $sut->warn();

        $this->assertSame(['a'], $received);
    }

    public function testItIsSilentWithoutListeners(): void
    {
        $sut = new DeprecatedEventsSubscriber([new DeprecatedEvent('old_action', '3.0.0')]);
        $sut->warn();

        $this->assertSame(0, \did_action('deprecated_hook_run'));
    }

    public function testTheEventKeepsItsValues(): void
    {
        $event = new DeprecatedEvent('old_filter', '3.0.0', 'new_filter', 'Use the new filter.', [['a']]);

        $this->assertSame('old_filter', $event->name());
        $this->assertSame('3.0.0', $event->version());
        $this->assertSame('new_filter', $event->replacement());
        $this->assertSame('Use the new filter.', $event->message());
        $this->assertSame([['a']], $event->args());
    }
}
