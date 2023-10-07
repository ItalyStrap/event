<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Unit;

use ItalyStrap\Event\EventSubscription;
use ItalyStrap\Tests\EventSubscriptionTrait;
use ItalyStrap\Tests\UnitTestCase;

class EventSubscriptionTest extends UnitTestCase
{
    use EventSubscriptionTrait;

    private function makeInstance(): EventSubscription
    {
        return new EventSubscription(
            $this->callback,
            $this->priority,
            $this->acceptedArgs
        );
    }
}
