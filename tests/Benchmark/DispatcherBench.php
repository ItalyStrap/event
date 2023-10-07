<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Benchmarks;

use Crell\Tukio\OrderedListenerProvider;
use ItalyStrap\Event\Dispatcher;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * @BeforeMethods({"setUp"})
 */
class DispatcherBench
{
    private ?Dispatcher $dispatcherWithNullListener = null;
    private ?\stdClass $event = null;
    private Dispatcher $dispatcherWithOrderedListener;

    public function setUp(): void
    {
        $this->dispatcherWithNullListener = new Dispatcher(new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                return [];
            }
        });
        $orderedListenerProvider = new OrderedListenerProvider();
        $orderedListenerProvider->addListener(function (\stdClass $event) {
            $event->value = 'Value printed';
            return $event;
        });
        $this->dispatcherWithOrderedListener = new Dispatcher($orderedListenerProvider);
        $this->event = new \stdClass();
    }

    /**
     * @revs (10000)
     * @iterations (5)
     */
    public function benchDispatchWithNullListeners(): void
    {
        $this->dispatcherWithNullListener->dispatch($this->event);
    }

    /**
     * @revs (10000)
     * @iterations (5)
     */
    public function benchDispatchWithOrderedListeners(): void
    {
        $this->dispatcherWithOrderedListener->dispatch($this->event);
    }
}
