# ItalyStrap PSR-14 Event API integrations with WordPress and WordPress Plugin API implementation

[![Tests Status](https://github.com/ItalyStrap/event/actions/workflows/test.yml/badge.svg)](https://github.com/ItalyStrap/event/actions)
[![Latest Stable Version](https://img.shields.io/packagist/v/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Total Downloads](https://img.shields.io/packagist/dt/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![License](https://img.shields.io/packagist/l/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
![PHP from Packagist](https://img.shields.io/packagist/php-v/italystrap/event)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/ItalyStrap/event?label=Scrutinizer)

PSR-14 Event Dispatcher implementation for WordPress and wrappers around the WordPress Plugin API (Events aka Hooks)

> [!IMPORTANT]
> It is still a WIP

Please, even if it works very well, also the PSR-14 implementation works very well, keep in mind that this is still a WIP until this package reach the version 1.x.x, for now it is a 0.x.x version (if you don't know what this means, please read the [SemVer](http://semver.org/) specification).

Personally I'm very proud of this package (like I'm proud for all the others 😊), at the end it was not very complicated to implement the PSR-14 standard, but I needed to hack a little bit the WordPress Plugin API to make it work as expected.

BTW, I'm using this package in production for my own projects and right now I have no issue, if you find some please, please, let me know opening an issue (or a PR if you want to fix it), here the link to the [issue tracker](https://github.com/ItalyStrap/event/issues).

The naming convention I used is: when you encounter the word `Global*` means that it is something related to the WordPress only because WP use global variables under the hood and I didn't want to use some prefix WP related, even if it is also for WordPress I never like prefixing my code with something related to a word that contains some correlation with it.

## Table Of Contents

* [Installation](#installation)
* [Introduction](#introduction)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Contributing](#contributing)
* [License](#license)
* [Credits](#credits)

## Installation

The best way to use this package is through Composer:

```CMD
composer require italystrap/event
```
This package adheres to the [SemVer](http://semver.org/) specification and will be fully backward compatible between minor versions.

## Introduction

Welcome to the documentation for ItalyStrap Event! In this introductory section, we will provide you with an overview of event-driven programming in the context of PHP development, WordPress and highlight the benefits of adopting this approach.

### Event-Driven Programming: An Overview

Event-driven programming is a paradigm widely used in software development to handle reactive scenarios and manage interactions within a software system. It revolves around the concept of events, which represent specific occurrences or interactions.

In event-driven programming, software components (listeners or subscribers) register their interest in specific events and define how they should respond when those events occur. This decoupled and reactive architecture promotes modularity, flexibility, and maintainability in complex applications.
Advantages of Event-Driven Programming

Event-driven programming offers several advantages that make it a valuable approach in various software development contexts:

#### 1. Loose Coupling

Events act as communication channels between different parts of a system, allowing them to interact without tight dependencies. This loose coupling enhances code reusability and promotes the separation of concerns.

#### 2. Scalability

Event-driven systems can effectively handle a large number of concurrent events, ensuring that the system remains responsive and adaptable to varying workloads.

#### 3. Flexibility

Event-driven architectures are flexible and extensible. New functionality can be added by introducing new events and listeners without major changes to existing code.

#### 4. Testability

Isolating event listeners allows for easier unit testing, as you can focus on testing individual components without the need for complex integration testing.

In the following sections of this documentation, we will delve deeper into the specifics of event-driven programming within the PHP ecosystem. We will explore how to work with events in WordPress, the core APIs for event handling, and how to seamlessly integrate the PSR-14 standard into your projects.

Let's continue our journey into the world of event-driven programming!

##  How an Event-Driven System Works

In this section, we'll provide an overview of how an event-driven system operates, explaining fundamental concepts, highlighting key components, and offering examples of common use cases for event-driven systems.

### Understanding Event-Driven Programming

Event-driven programming is a software architecture that relies on events to trigger and manage the flow of a program. Here are some key concepts to grasp:

#### Events

Events represent specific occurrences or interactions within a system. They serve as signals or notifications that something has happened or needs attention. Events can range from user actions (e.g., button clicks) to system-generated notifications (e.g., data updates).

#### Event Handlers (Listeners)

Event handlers, often referred to as listeners or subscribers, are components responsible for responding to specific events. These listeners register their interest in particular events and execute predefined actions when those events occur.

#### Event Loop

The event loop is a fundamental part of event-driven systems. It continuously monitors for events and dispatches them to the appropriate event handlers. The loop ensures that events are processed in the order they occur, providing a responsive and non-blocking execution environment.

### Key Components of an Event-Driven System

An event-driven system typically consists of the following components:

#### 1. Events

Events define what can happen in the system and encapsulate relevant data associated with those occurrences.

#### 2. Event Handlers (Listeners)

Event handlers, or listeners, respond to specific events by executing the corresponding actions or functions.

#### 3. Event Loop

The event loop manages the flow of events, ensuring they are dispatched to the correct listeners.

#### 4. Dispatcher

The dispatcher is responsible for coordinating the dispatch of events to their respective listeners. It acts as the central hub for event handling.

### Common Use Cases for Event-Driven Systems

Event-driven systems are versatile and find applications in various domains. Here are some common use cases:

### 1. User Interfaces

Graphical user interfaces (GUIs) often rely on event-driven architectures to respond to user interactions such as button clicks, mouse movements, and keyboard inputs.

#### 2. Real-Time Applications

Systems requiring real-time processing, such as online games, chat applications, and financial trading platforms, benefit from event-driven designs to ensure responsiveness.

#### 3. Notifications and Alerts

Event-driven systems are well-suited for delivering notifications and alerts based on specific triggers or conditions.

#### 4. IoT (Internet of Things)

IoT applications use event-driven principles to manage and process data generated by connected devices and sensors.

In the upcoming sections, we'll explore how event-driven programming is implemented in WordPress, dive into the core APIs for event handling, and demonstrate how to seamlessly integrate the PSR-14 standard into your projects.

Let's continue our journey into the world of event-driven programming!

## How the WordPress Event System Works: Core APIs

In this section, we will dive into the core APIs used for event handling in WordPress. These APIs are essential for managing actions and filters, which serve as the building blocks of WordPress event-driven architecture.
Unfortunately WordPress Event API use global variables under the hood, and this is a bad practice, but we can't do anything about it because WordPress will never change this, so we need to live with it and close our nose.

### Actions and Filters

#### Actions

Actions in WordPress are events triggered at specific points during the execution of a request. Actions are instrumental for executing side effects or custom code at predefined moments within the WordPress lifecycle.

Actions do not return any values; instead, they serve as a signal for event handlers to perform tasks. When an action is fired, all attached action handlers (known as "action hooks") are executed sequentially.

Example of dispatching a simple action event (hook) without any arguments in WordPress:

```php
\do_action('my_custom_action');
```

And here the same event as above but with arguments this time:

```php
\do_action('my_custom_action', $arg1, $arg2);
```

Here, 'my_custom_action' represents the event name or hook. WordPress provides numerous predefined action hooks that developers can leverage to extend and customize the platform.
To naming a few registered in the core:

* `init`
* `wp_loaded`
* `admin_init`
* `admin_menu`
* and so on...

#### Filters

Filters in WordPress are events similar to actions but with an important distinction: filters allow modification of data before it is used elsewhere in the system. Filters are used when you want to modify a value, content, or data passed through the filter (yes, you could also do this with actions, but I will talk about it later).

Filters return a modified or unaltered value, always, and multiple filter handlers (listener) can be applied in sequence as you can do with actions. Filters are widely used for customizing and manipulating data within WordPress.

Example of defining a filter hook and applying a filter:

```php
$data_to_modify = 'Some data to modify';
$filtered_data = \apply_filters('my_custom_filter', $data_to_modify);
```

Here, 'my_custom_filter' denotes the filter's event name or hook. WordPress allows developers to create their custom filters in addition to utilizing predefined filters.

Understanding the concept of hook/event names is crucial when working with actions and filters in WordPress. Event names serve as identifiers for specific points in the execution flow where custom code can be attached. Developers can use both WordPress-defined hooks and create custom hooks to extend and customize WordPress functionality.

So to remember **actions** and **filters** are mostly the same thing, and they are the **dispatcher** of the event system in WordPress.

WordPress use string as event name and in the WordPress documentation they are called **hooks**.

**Dangerous things to know about dispatching actions and filters:**
Never ever dispatch an event inside a constructor of a class, this is a very bad practice and if you do that you are a bad developer.

[🆙](#table-of-contents)

## Basic Usage

The `\ItalyStrap\Event\GlobalDispatcher::class`, `\ItalyStrap\Event\GlobalOrderedListenerProvider::class` and `\ItalyStrap\Event\GlobalState::class` are wrappers around the [WordPress Plugin API](https://developer.wordpress.org/plugins/hooks/)

The `\ItalyStrap\Event\Dispatcher::class` and `\ItalyStrap\Event\GlobalOrderedListenerProvider::class` implement the [PSR-14](https://www.php-fig.org/psr/psr-14/) Event Dispatcher.

### Simple example for actions

```php
$listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
// Listen for `event_name`
$listenerProvider->addListener( 'event_name', function () { echo 'Event Called'; }, 10 );

$globalDispatcher = new \ItalyStrap\Event\GlobalDispatcher();
// This will echo 'Event Called' on `event_name`
$globalDispatcher->trigger( 'event_name' );
```

### Simple example for filters

```php
$listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
// Listen for `event_name`
$listenerProvider->addListener( 'event_name', function ( array $value ) {
    // $value['some-key'] === 'some-value'; true

    // Do your stuff here in the same ways you do with filters
    return $value;
}, 10 );

/** @var array $value */
$value = [ 'some-key' => 'some-value' ];
$globalDispatcher = new \ItalyStrap\Event\GlobalDispatcher();
// This will filter '$value' on `event_name`
$filtered_value = $globalDispatcher->filter( 'event_name', $value );
```

Ok, so, for now it is very straightforward, you will use it like you use the WordPress Plugin API but more OOP oriented,
you can inject the `GlobalDispatcher::class` or `GlobalOrderedListenerProvider::class` into yours classes.

### The SubscriberRegister

What about the Subscriber Register?
Here a simple example:

```php
use ItalyStrap\Event\GlobalDispatcher;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscriberInterface;

// Your class must implement the ItalyStrap\Event\SubscriberInterface
class MyClassSubscriber implements SubscriberInterface {

    // Now add the method from the interface and return an iterable with
    // event name and the method to executed on the event
    public function getSubscribedEvents(): iterable {
        return ['event_name' => 'methodName'];
    }

    public function methodName(/* could have some arguments */){
        // Do some stuff with hooks
    }
}

$subscriber = new MyClassSubscriber();

$globalDispatcher = new GlobalDispatcher();
$listenerProvider = new GlobalOrderedListenerProvider();
$subscriberRegister = new SubscriberRegister($listenerProvider);
$subscriberRegister->addSubscriber($subscriber);

// It will execute the subscriber MyClassSubscriber::methodName
$globalDispatcher->trigger('event_name', $some_value);
// or
$globalDispatcher->filter('event_name', $some_value);
```
A subscriber is a class that implements the `ItalyStrap\Event\SubscriberInterface::class` interface
and could be the listener itself or a class wrapper that delegates the execution of the method on certain event
to the class it wraps.

The `ItalyStrap\Event\SubscriberInterface::getSubscribedEvents()` must return an iterable like those:

```php
use ItalyStrap\Event\SubscriberInterface;

class MyClassSubscriber implements SubscriberInterface {

    // Just one event => method form generator
    public function getSubscribedEvents(): iterable {
        yield 'event_name' => 'method_name';
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface;

class MyClassSubscriber implements SubscriberInterface {

    // Just one event => method form Iterators
    public function getSubscribedEvents(): iterable {

        yield new \ArrayObject(['event_name' => 'methodName']);

        yield new \ItalyStrap\Config\Config(['event_name' => 'methodName']);

        yield (new \ItalyStrap\Config\Config())->add( 'event_name', 'methodName' );
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface;

class MyClassSubscriber implements SubscriberInterface {

    // Just one event => method
    public function getSubscribedEvents(): iterable {
        return ['event_name' => 'method_name'];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface;

class MyClassSubscriber implements SubscriberInterface {

    // Multiple events => methods
    public function getSubscribedEvents(): iterable {
        return [
            'event_name' => 'method_name',
            'event_name2' => 'method_name2'
            // ... more event => method
        ];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface as Subscriber;

class MyClassSubscriber implements Subscriber {

    public function getSubscribedEvents(): iterable {
        // Event with method and priority (for multiple events the logic is the same as above)
        return [
            'event_name' => [
                Subscriber::CALLBACK	=> 'method_name',
                Subscriber::PRIORITY	=> 20, // 10 default
            ],
            // ... more event => method
        ];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface as Subscriber;

class MyClassSubscriber implements Subscriber {

    public function getSubscribedEvents(): iterable {
        // Event with method, priority and accepted args (for multiple events the logic is the same as above)
        return [
           'event_name' => [
               Subscriber::CALLBACK	    => 'method_name',
               Subscriber::PRIORITY	    => 20, // 10 default
               Subscriber::ACCEPTED_ARGS	=> 4 // 3 default
           ],
            // ... more event => method
       ];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

```php
use ItalyStrap\Event\SubscriberInterface as Subscriber;

class MyClassSubscriber implements Subscriber {

    public function getSubscribedEvents(): iterable {
        // Event with methods, priority and accepted args (for multiple events the logic is the same as above)
        return [
           'event_name' => [
                [
                    Subscriber::CALLBACK	    => 'method_name',
                    Subscriber::PRIORITY	    => 20, // 10 default
                    Subscriber::ACCEPTED_ARGS	=> 4 // 3 default
                ],
                [
                    Subscriber::CALLBACK	    => 'method_name2',
                    Subscriber::PRIORITY	    => 20, // 10 default
                    Subscriber::ACCEPTED_ARGS	=> 4 // 3 default
                ],
            ],
            // ... more event => method
       ];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}
```

In case the subscriber has a lot of events to subscribe it is better to [separate](https://en.wikipedia.org/wiki/Separation_of_concerns) the business logic from the
subscriber in another class and then use the subscriber to do the registration of the other class like this:

```php
use ItalyStrap\Event\GlobalDispatcher;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscriberInterface;

class MyBusinessLogic {
    public function methodOne() {
        // Do some stuff
    }
    public function methodTwo() {
        // Do some stuff
    }
    public function methodThree() {
        // Do some stuff
    }
}

class MyClassSubscriber implements SubscriberInterface {
    /**
     * @var MyBusinessLogic 
     */
    private $logic;
    public function __construct( MyBusinessLogic $logic ) {
        $this->logic = $logic;
    }

    public function getSubscribedEvents(): array {
        return [
            'event_name_one' => 'onEventNameOne',
            'event_name_two' => 'onEventNameTwo',
            'event_name_three' => 'onEventNameThree',
        ];
    }
    
    public function onEventNameOne(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodOne();
    }
    
    public function onEventNameTwo(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodTwo();
    }
    
    public function onEventNameThree(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodThree();
    }
}
$logic = new MyBusinessLogic();
$subscriber = new MyClassSubscriber( $logic );

$dispatcher = new GlobalDispatcher();
$subscriber_register = new SubscriberRegister( $dispatcher );
$subscriber_register->addSubscriber( $subscriber );

// It will execute the subscriber MyClassSubscriber::methodName
$dispatcher->trigger( 'event_name' );
// or
$dispatcher->filter( 'event_name', ['some_value'] );

// You can also remove a listener:
$subscriber_register->removeSubscriber( $subscriber );

// The instance of the subscriber you want to remove MUST BE the same instance of the subscriber you
// added earlier, and BEFORE you dispatch the event.
```

This library is similar to the [Symfony Event Dispatcher](https://symfony.com/doc/current/components/event_dispatcher.html)

[🆙](#table-of-contents)

## Advanced Usage

If you want more power you can use the [Empress library](https://github.com/ItalyStrap/empress) with this library
The benefit is that now you can do auto-wiring for your application, lazy loading you listener/subscriber and so on.

```php
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynConfig;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Event\GlobalDispatcher;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberInterface;

// From Subscriber.php
class Subscriber implements SubscriberInterface {

	public int $check = 0;

	private \stdClass $stdClass;

	public function __construct(\stdClass $stdClass) {
		$this->stdClass = $stdClass;
	}

	public function getSubscribedEvents(): array {
	    yield 'event' => $this;
	}

	public function __invoke() {
		echo 'Some text';
	}
}

// From your bootstrap.php file

// Create a new InjectorContainer
$injector = new Injector();

// This is optional, you could share the injector instance
// if you need this instance inside a class for registering stuff
// Remember that the Auryn\Injector is not a service locator
// Do not use it for locating services
$injector->share($injector);

// Now it's time to create a configuration for dependencies to inject in the AurynConfig
$dependencies = ConfigFactory::make([
    // Share the instances of the GlobalDispatcher and SubscriberRegister
    AurynConfig::SHARING	=> [
        GlobalDispatcher::class,
        SubscriberRegister::class,
    ],
    // Now add in the array all your subscribers that implement the ItalyStrap\Event\SubscriberInterface
    // The instances create are shared by default for later removing like you se above.
    SubscribersConfigExtension::SUBSCRIBERS	=> [
        Subscriber::class,
    ],
    // You can also add more configuration for the AurynConfig https://github.com/ItalyStrap/empress
]);

// This will instantiate the EventResolverExtension::class
$eventResolver = $injector->make(SubscribersConfigExtension::class, [
    // In the EventResolverExtension object you can pass a config key value pair for adding or not listener at runtime
    // from your theme or plugin options
    ':config'	=> ConfigFactory::make([
        // If the 'option_key_for_subscriber' is true than the Subscriber::class will load
        'option_key_for_subscriber' => Subscriber::class // Optional
    ]),
]);

// Create the object for the AurynConfig::class and pass the instance of $injector and the dependencies collection
$empress = new \ItalyStrap\Empress\AurynConfig($injector, $dependencies);

// Is the same as above if you want to use Auryn and you have shared the Auryn instance:
$empress = $injector->make(AurynConfig::class, [
    ':dependencies'  => $dependencies
]);

// Pass the $event_resolver object created earlier
$empress->extend($eventResolver);

// When you are ready call the resolve() method for auto-wiring your application
$empress->resolve();


$this->expectOutputString( 'Some text' );
($injector->make(GlobalDispatcher::class))->trigger('event');
// or
$dispatcher = $injector->make(GlobalDispatcher::class);
$dispatcher->trigger('event');

// $dispatcher will be the same instance because you have shared it in the above code
```

### Lazy Loading a subscriber

To lazy load a subscriber you can simply add in the AurynConfig configuration a new value
for proxy, see the example below:

```php
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynConfig;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Event\GlobalDispatcher;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberInterface;

// From MyBusinessLogic.php
class MyBusinessLogic {
    public function __construct(/*Heavy dependencies*/){
        // Initialize
    }
    public function methodOne() {
        // Do some stuff
    }
    public function methodTwo() {
        // Do some stuff
    }
    public function methodThree() {
        // Do some stuff
    }
}
// From MyClassSubscriber.php
class MyClassSubscriber implements SubscriberInterface {

    private MyBusinessLogic $logic;
    public function __construct(MyBusinessLogic $logic) {
        // This will be the proxy version of the $logic object
        $this->logic = $logic;
    }

    public function getSubscribedEvents(): array {
        // The first method that will be called will sobstitute the
        // proxy version of the object with the real one.
        return [
            'event_name_one'    => 'onEventNameOne',
            'event_name_two'    => 'onEventNameTwo',
            'event_name_three'  => 'onEventNameThree',
        ];
    }

    public function onEventNameOne(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodOne();
    }
    
    public function onEventNameTwo(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodTwo();
    }
    
    public function onEventNameThree(/* may be with some arguments if you use the ::filter() method of the dispatcher */){
        $this->logic->methodThree();
    }
}

// From your bootstrap.php file

// Create a new InjectorContainer
$injector = new Injector();

// This is optional, you could share the injector instance
// if you need this instance inside a class for registering stuff
// Remember that the Auryn\Injector is not a service locator
// Do not use it for locating services
$injector->share($injector);

// Now it's time to create a configuration for dependencies to inject in the AurynConfig
$dependencies = ConfigFactory::make([
    // Share the instances of the GlobalDispatcher and SubscriberRegister
    AurynConfig::SHARING	=> [
        GlobalDispatcher::class,
        SubscriberRegister::class,
    ],
    // Now we declare what class we need to lazy load
    // In our case is the MyBusinessLogic::class injected in the MyClassSubscriber::class
    AurynConfig::PROXY  => [
        MyBusinessLogic::class,
    ],
    // Now add in the array all your subscribers that implement the ItalyStrap\Event\SubscriberInterface
    // The instances create are shared by default for later removing like you se above.
    SubscribersConfigExtension::SUBSCRIBERS	=> [
        MyClassSubscriber::class,
    ],
    // You can also add more configuration for the AurynConfig https://github.com/ItalyStrap/empress
]);

// This will instantiate the EventResolverExtension::class
$event_resolver = $injector->make( SubscribersConfigExtension::class, [
    // In the EventResolverExtension object you can pass a config key value pair for adding or not listener at runtime
    // from your theme or plugin options
    ':config'	=> ConfigFactory::make([
        // If the 'option_key_for_subscriber' is true than the Subscriber::class will load
        'option_key_for_subscriber' => Subscriber::class // Optional
    ]),
] );

// Create the object for the AurynConfig::class and pass the instance of $injector and the dependencies collection
$empress = new AurynConfig( $injector, $dependencies );

// Is the same as above if you want to use Auryn and you have shared the Auryn instance:
$empress = $injector->make( AurynConfig::class, [
    ':dependencies'  => $dependencies
] );

// Pass the $event_resolver object created earlier
$empress->extend( $event_resolver );

// When you are ready call the resolve() method for auto-wiring your application
$empress->resolve();

$dispatcher = $injector->make(GlobalDispatcher::class);
$dispatcher->trigger('event_name_one');
$dispatcher->trigger('event_name_two');
$dispatcher->trigger('event_name_three');
```
Remember that the proxy version of an object is a "dumb" object that do nothing until you
call some method, and the real object will be executed, this is useful for run code only when
you need it to run.

Example with pseudocode;
```php
\do_action('save_post', [$proxyObject, 'executeOnlyOnSavePost']);
```

You can find more information about the [`Empress\AurynConfig` here](https://github.com/ItalyStrap/empress)
You can find an implementation in the [ItalyStrap Theme Framework](https://github.com/ItalyStrap/italystrap)

> TODO https://inpsyde.com/en/remove-wordpress-hooks/

[🆙](#table-of-contents)

## Contributing

All feedback / bug reports / pull requests are welcome.

[🆙](#table-of-contents)

## License

Copyright (c) 2019 Enea Overclokk, ItalyStrap

This code is licensed under the [MIT](LICENSE).

[🆙](#table-of-contents)

## Credits

### For the Event implementation

* [Carl Alexander](https://carlalexander.ca/designing-system-wordpress-event-management/)
* [Carl Alexander](https://carlalexander.ca/mediator-pattern-wordpress/)

### For the PsrDispatcher implementation

* [Symfony](https://symfony.com/doc/current/components/event_dispatcher.html)
* [Larry Garfield](https://github.com/Crell/Tukio)
* [Timothy Jacobs](https://github.com/iron-bound-designs/psr-14-wp)
