<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Integration;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\Tests\ClassWithDispatchDependency;
use ItalyStrap\Tests\IntegrationTestCase;
use ItalyStrap\Tests\Listener;
use ItalyStrap\Tests\Subscriber;

class IntegrationTest extends IntegrationTestCase
{
    private function makeDispatcher(): EventDispatcher
    {
        return new EventDispatcher();
    }

    public function testItShouldOutputTextOnEventName()
    {
        $sut = $this->makeDispatcher();

        $sut->addListener('event_name', function () {
            echo 'Value printed';
        });

        $this->expectOutputString('Value printed');
        $sut->dispatch('event_name');
    }

//    public function testItShouldOutputTextOnEventNamsgfsfe()
//    {
//        $sut = $this->makeDispatcher();
//
//        $sut->addListener('event_name', function ($arg) {
//            return $arg;
//        });
//
//        $result = $sut->dispatch('event_name', 'Value returned');
//        $this->assertSame('Value returned', $result);
//    }

    public function testClassWithDispatchDependency()
    {
        $sut = $this->makeDispatcher();

        $some_class = new ClassWithDispatchDependency($sut);

        $sut->addListener(
            ClassWithDispatchDependency::EVENT_NAME,
            fn(string $value) => 'New value'
        );

        $some_class->filterValue();

        $this->assertStringContainsString('New value', $some_class->value(), '');
    }

    public function testSubscriberShouldEchoTextWhenEventIsExecuted()
    {

        $sut = $this->makeDispatcher();

        $subscriber = new class implements SubscriberInterface {
            /**
             * @inheritDoc
             */
            public function getSubscribedEvents(): array
            {
                return [
//                    'invocable_event_name'    => $this,
                    'event_name'    => 'method',
                    'other_event_name'  => [
                        [
                            SubscriberInterface::CALLBACK       => 'onCallback',
                            SubscriberInterface::PRIORITY       => 20,
                            SubscriberInterface::ACCEPTED_ARGS  => 6,
                        ],
                        [
                            SubscriberInterface::CALLBACK       => 'onCallback',
                            SubscriberInterface::PRIORITY       => 10,
                            SubscriberInterface::ACCEPTED_ARGS  => 6,
                        ],
                    ],
                ];
            }

            public function method()
            {
                echo 'Value printed';
            }

            public function onCallback(string $filtered)
            {
                return $filtered . ' Value printed';
            }

            public function __invoke(string $filtered)
            {
                echo 'Value invoked';
            }
        };

        $register = new SubscriberRegister(new EventDispatcher());
        $register->addSubscriber($subscriber);

        $this->expectOutputString('Value printed');
        $sut->dispatch('event_name');

        $filtered = (string) $sut->filter('other_event_name', '');
        $this->assertStringContainsString('Value printed Value printed', $filtered, '');

//        $filtered = (string) $sut->filter('invocable_event_name', '');
//        $this->assertStringContainsString('Value invoked', $filtered, '');
    }

    /**
     * @test
     */
    public function itShouldPrintText()
    {

        $injector = new Injector();
        $injector->share($injector);

        $injector->alias(EventDispatcherInterface::class, EventDispatcher::class);
        $injector->share(EventDispatcherInterface::class);
        $injector->share(SubscriberRegister::class);
        $event_resolver = $injector->make(SubscribersConfigExtension::class, [
            ':config'   => ConfigFactory::make([
                Subscriber::class   => false
            ]),
        ]);

        $dependencies = ConfigFactory::make([
//          AurynResolver::ALIASES  => [
//              HooksInterface::class   => Hooks::class,
//          ],
//          AurynResolver::SHARING  => [
//              HooksInterface::class,
//              EventManager::class,
//          ],
            SubscribersConfigExtension::SUBSCRIBERS => [
                Subscriber::class,
//              Subscriber::class   => false,
            ],
        ]);

//      $empress = new AurynResolver( $injector, $dependencies );
        $empress = $injector->make(AurynResolver::class, [
            ':dependencies' => $dependencies
        ]);
        $empress->extend($event_resolver);
        $empress->resolve();

        $this->expectOutputString('Some text');
        ( $injector->make(EventDispatcher::class) )->dispatch('event');
    }

    private function configExample()
    {

        $test = [
            'hook_name => callback'                 => [
                [
                    'hook_name'             => 'callback'
                ]
            ],
            'hook_name => [callback|priority]'      => [
                [
                    'hook_name' => [
                        SubscriberInterface::CALLBACK       => 'callback',
                        SubscriberInterface::PRIORITY       => 20,
                    ]
                ]
            ],
            'hook_name => [callback|priority|args]' => [
                [
                    'hook_name' => [
                        SubscriberInterface::CALLBACK       => 'callback',
                        SubscriberInterface::PRIORITY       => 20,
                        SubscriberInterface::ACCEPTED_ARGS  => 6,
                    ]
                ]
            ],
        ];

        $config = [
            'subscribers'   => [
                Subscriber::class,
            ],
            'listeners' => [
                Listener::class     => [
                    'event_name'    => '',
                    'method'    => '',
                    'priority'  => '',
                    'args'  => '',
                ]
            ],
        ];
    }
}
