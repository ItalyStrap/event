<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventDispatcher;
use Psr\EventDispatcher\ListenerProviderInterface;

class IntegrationTestCase extends WPTestCase
{
    protected \IntegrationTester $tester;
    protected ?\Psr\EventDispatcher\ListenerProviderInterface $listener = null;

    public function setUp(): void
    {
        // Before...
        parent::setUp();

        $_SERVER['REQUEST_TIME'] = \time();

        global $wp_filter, $wp_actions;
        $wp_filter = $wp_actions = [];
//
//        $this->listener = new class implements ListenerProviderInterface {
//            /**
//             * @inheritDoc
//             */
//            public function getListenersForEvent(object $event): iterable
//            {
//                // TODO: Implement getListenersForEvent() method.
//            }
//        };

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        global $wp_filter, $wp_actions;
        unset($wp_filter, $wp_actions);

        // Then...
        parent::tearDown();
    }
}
