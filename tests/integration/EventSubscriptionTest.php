<?php

declare(strict_types=1);

namespace ItalyStrap\Event\Tests\Integration;

use ItalyStrap\Event\EventSubscription;
use ItalyStrap\Tests\EventSubscriptionTrait;
use ItalyStrap\Tests\IntegrationTestCase;

class EventSubscriptionTest extends IntegrationTestCase
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
