<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Event\SubscriberInterface;

trait SubscriberRegisterTestTrait
{
    public function subscriberProvider(): iterable
    {

        $obj = new class {
            public function __invoke(): bool
            {
                return true;
            }
        };

        yield 'event_name => object callable'                    => [
            [
                'event_name'            => $obj,
            ]
        ];

        $obj = new class {
            public function run(): bool
            {
                return true;
            }
        };

        yield 'event_name => object with method run'                    => [
              [
                  'event_name'           => [$obj, 'run'],
              ]
        ];

        yield 'event_name => callable'                    => [
              [
                  'event_name'            => function () {
                  },
              ]
        ];

        yield 'event_name_with_callable_method_from_SubscriberMock'         => [
            [
                'event_name'            => 'executeCallable',
                'event_name1' => [
                    SubscriberInterface::CALLBACK       => 'executeCallable',
                    SubscriberInterface::PRIORITY       => 20,
                ],
            ]
        ];

        yield 'event_name => callback'                    => [
            [
                'event_name'            => 'ItalyStrap\Tests\on_callback',
                'event_name1'           => 'ItalyStrap\Tests\on_callback',
            ]
        ];

        yield 'event_name => [callback|priority]'     => [
            [
                'event_name' => [
                    SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                    SubscriberInterface::PRIORITY       => 20,
                ],
                'event_name1' => [
                    SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                    SubscriberInterface::PRIORITY       => 20,
                ],
            ]
        ];

        yield 'event_name => [callback|priority|args]'    => [
            [
                'event_name' => [
                    SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                    SubscriberInterface::PRIORITY       => 20,
                    SubscriberInterface::ACCEPTED_ARGS  => 6,
                ],
                'event_name1' => [
                    SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                    SubscriberInterface::PRIORITY       => 20,
                    SubscriberInterface::ACCEPTED_ARGS  => 6,
                ],
            ]
        ];

        yield 'event_name => [[callback|priority|args]]'  => [
            [
                'event_name' => [
                    [
                        SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                        SubscriberInterface::PRIORITY       => 10,
                        SubscriberInterface::ACCEPTED_ARGS  => 6,
                    ],
                    [
                        SubscriberInterface::CALLBACK       => 'ItalyStrap\Tests\on_callback',
                        SubscriberInterface::PRIORITY       => 20,
                        SubscriberInterface::ACCEPTED_ARGS  => 6,
                    ],
                ],
            ]
        ];
    }
}
