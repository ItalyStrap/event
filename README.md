# ItalyStrap Event API

[![Build Status](https://travis-ci.org/ItalyStrap/event.svg?branch=master)](https://travis-ci.org/ItalyStrap/event)
[![Latest Stable Version](https://img.shields.io/packagist/v/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Total Downloads](https://img.shields.io/packagist/dt/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![License](https://img.shields.io/packagist/l/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
![PHP from Packagist](https://img.shields.io/packagist/php-v/italystrap/event)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/ItalyStrap/event?label=Scrutinizer)

WordPress Hooks Events plus Psr-14 Events API the OOP way

**It is still a WIP**

## Table Of Contents

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Contributing](#contributing)
* [License](#license)

## Installation

The best way to use this package is through Composer:

```CMD
composer require italystrap/event
```
This package adheres to the [SemVer](http://semver.org/) specification and will be fully backward compatible between minor versions.

## Basic Usage

The `EventDispatcher::class` is a wrapper around the (WordPress Plugin API)[https://developer.wordpress.org/plugins/hooks/]

### Simple example for actions

```php
use ItalyStrap\Event\EventDispatcher;

$dispatcher = new EventDispatcher();
// Listen for `event_name`
$dispatcher->addListener( 'event_name', function () { echo 'Event Called'; }, 10 );

// This will echo 'Event Called' on `event_name`
$dispatcher->dispatch( 'event_name' );
```

### Simple example for filters

```php
use ItalyStrap\Event\EventDispatcher;

$dispatcher = new EventDispatcher();
// Listen for `event_name`
$dispatcher->addListener( 'event_name', function ( array $value ) {
    // $value['some-key'] === 'some-value'; true

    // Do your stuff here in the same ways you do with filters
    return $value;
}, 10 );

/** @var array $value */
$value = [ 'some-key' => 'some-value' ];
// This will filters '$value' on `event_name`
$filtered_value = $dispatcher->filter( 'event_name', $value );
```

Ok, so, for now it is very straightforward, you will use it like you use the WordPress Plugin API but more OOP oriented,
you can inject the `EventDispatcher::class` into yours classes.

```php
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;

$dispatcher = new EventDispatcher();

class MyClass {

    /**
     * @var EventDispatcherInterface 
     */
    private $dispatcher;
    public function __construct( EventDispatcherInterface $dispatcher ) {
        $this->dispatcher = $dispatcher;
    }

    public function doSomeStuffWithDispatcher() {
        // Do your stuff here with hooks
        // $this->dispatcher->dispatch() or $this->dispatcher->addEventListener() or $this->dispatcher->removeEventListener()
    }
}

$my_class = new MyClass( $dispatcher );
$my_class->doSomeStuffWithDispatcher();
```

### The SubscriberRegister

What about the Subscriber Register?
Here a simple example:

```php
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscriberInterface;

// Your class must implements the ItalyStrap\Event\SubscriberInterface
class MyClassSubscriber implements SubscriberInterface {

    // Now add the method from the interface and return an iterable with
    // event name and the method to executed on the event
    public function getSubscribedEvents(): iterable {
        return ['event_name' => 'methodName'];
    }

    public function methodName(/* could have some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}

$subscriber = new MyClassSubscriber();

$dispatcher = new EventDispatcher();
$subscriber_register = new SubscriberRegister( $dispatcher );
$subscriber_register->addSubscriber( $subscriber );

// It will execute the subscriber MyClassSubscriber::methodName
$dispatcher->dispatch( 'event_name' );
// or
$dispatcher->filter( 'event_name', $some_value );
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

In case the subscriber has a lot of events to subscribe it is better to (separate)[https://en.wikipedia.org/wiki/Separation_of_concerns] the business logic from the
subscriber in another class and then use the subscriber to do the registration of the other class like this:

```php
use ItalyStrap\Event\EventDispatcher;
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

$dispatcher = new EventDispatcher();
$subscriber_register = new SubscriberRegister( $dispatcher );
$subscriber_register->addSubscriber( $subscriber );

// It will execute the subscriber MyClassSubscriber::methodName
$dispatcher->dispatch( 'event_name' );
// or
$dispatcher->filter( 'event_name', ['some_value'] );

// You can also remove a listener:
$subscriber_register->removeSubscriber( $subscriber );

// The instance of the subscriber you want to remove MUST BE the same instance of the subscriber you
// added earlier and BEFORE you dispatch the event.
```

This library is similar to the (Symfony Event Dispatcher)[https://symfony.com/doc/current/components/event_dispatcher.html]

### Example with WordPress event name
```php
// Filter the title
use ItalyStrap\Event\EventDispatcher;

$dispatcher = new EventDispatcher();
$dispatcher->filter( 'the_title', function ( string $title ): string {
    return \mb_strtoupper( $title ); // A very dumb example
} );

// Execute some action
$dispatcher->dispatch( 'after_setup_theme', function (): void {
    // Bootstrap your logic for theme configuration
} );
```

## Advanced Usage

If you want more power you can use the (Empress library)[https://github.com/ItalyStrap/empress] with this library
The benefit is that now you can do auto-wiring for your application, lazy loading you listener/subscriber and so on.

```php
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventResolverExtension;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\SubscriberInterface;

// From Subscriber.php
class Subscriber implements SubscriberInterface {

	public $check = 0;

	/**
	 * @var \stdClass
	 */
	private $stdClass;

	/**
	 * Subscriber constructor.
	 * @param \stdClass $stdClass
	 */
	public function __construct( \stdClass $stdClass  ) {
		$this->stdClass = $stdClass;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return [
			'event'	=> 'method',
		];
	}

	public function method() {
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

// Now it's time to create a configuration for dependencies to inject in the AurynResolver
$dependencies = ConfigFactory::make([
    // Share the instances of the EventDispatcher and SubscriberRegister
    AurynResolver::SHARING	=> [
        EventDispatcher::class,
        SubscriberRegister::class,
    ],
    // Now add in the array all your subscribers that implemente the ItalyStrap\Event\SubscriberInterface
    // The instances create are shared by default for later removing like you se above.
    EventResolverExtension::SUBSCRIBERS	=> [
        Subscriber::class,
    ],
    // You can also add more configuration for the AurynResolver https://github.com/ItalyStrap/empress
]);

// This wil instantiate the EventResolverExtension::class
$event_resolver = $injector->make( EventResolverExtension::class, [
    // In the EventResolverExtension object you can pass a config key value pair for adding or not listener at runtime
    // from your theme or plugin options
    ':config'	=> ConfigFactory::make([
        // If the 'option_key_for_subscriber' is true than the Subscriber::class will load
        'option_key_for_subscriber' => Subscriber::class // Optional
    ]),
] );

// Create the object for the AurynResolver::class and pass the instance of $injector and the dependencies collection
$empress = new AurynResolver( $injector, $dependencies );

// Is the same as above if you want to use Auryn and you have shared the Auryn instance:
$empress = $injector->make( AurynResolver::class, [
    ':dependencies'  => $dependencies
] );

// Pass the $event_resolver object created earlier
$empress->extend( $event_resolver );

// When you are ready call the resolve() method for auto-wiring your application
$empress->resolve();


$this->expectOutputString( 'Some text' );
( $injector->make( EventDispatcher::class ) )->dispatch( 'event' );
// or
$dispatcher = $injector->make( EventDispatcher::class );
$dispatcher->dispatch( 'event' );

// $dispatcher will be the same instance because you have shared it in the above code
```
### Lazy Loading a subscriber

To lazy load a subscriber you can simply add in the AurynResolver configuration a new value
for proxy, see the example below:
```php
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Empress\AurynResolver;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\EventResolverExtension;
use ItalyStrap\Event\EventDispatcher;
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
    /**
     * @var MyBusinessLogic 
     */
    private $logic;
    public function __construct( MyBusinessLogic $logic ) {
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

// Now it's time to create a configuration for dependencies to inject in the AurynResolver
$dependencies = ConfigFactory::make([
    // Share the instances of the EventDispatcher and SubscriberRegister
    AurynResolver::SHARING	=> [
        EventDispatcher::class,
        SubscriberRegister::class,
    ],
    // Now we declare what class we need to lazy load
    // In our case is the MyBusinessLogic::class injected in the MyClassSubscriber::class
    AurynResolver::PROXY  => [
        MyBusinessLogic::class,
    ],
    // Now add in the array all your subscribers that implemente the ItalyStrap\Event\SubscriberInterface
    // The instances create are shared by default for later removing like you se above.
    EventResolverExtension::SUBSCRIBERS	=> [
        MyClassSubscriber::class,
    ],
    // You can also add more configuration for the AurynResolver https://github.com/ItalyStrap/empress
]);

// This wil instantiate the EventResolverExtension::class
$event_resolver = $injector->make( EventResolverExtension::class, [
    // In the EventResolverExtension object you can pass a config key value pair for adding or not listener at runtime
    // from your theme or plugin options
    ':config'	=> ConfigFactory::make([
        // If the 'option_key_for_subscriber' is true than the Subscriber::class will load
        'option_key_for_subscriber' => Subscriber::class // Optional
    ]),
] );

// Create the object for the AurynResolver::class and pass the instance of $injector and the dependencies collection
$empress = new AurynResolver( $injector, $dependencies );

// Is the same as above if you want to use Auryn and you have shared the Auryn instance:
$empress = $injector->make( AurynResolver::class, [
    ':dependencies'  => $dependencies
] );

// Pass the $event_resolver object created earlier
$empress->extend( $event_resolver );

// When you are ready call the resolve() method for auto-wiring your application
$empress->resolve();

$dispatcher = $injector->make( EventDispatcher::class );
$dispatcher->dispatch( 'event_name_one' );
$dispatcher->dispatch( 'event_name_two' );
$dispatcher->dispatch( 'event_name_three' );
```
Remember that the proxy version of an object is a "dumb" object that do nothing until you
call some method, and the real object will be executed, this is useful for run code only when
you need it to run.

Example with pseudo code;
```php
\do_action('save_post', [$proxyObject, 'executeOnlyOnSavePost']);
```

You can find more information about the (EmpressAurynResolver here)[https://github.com/ItalyStrap/empress]
You can find an implementation in the (ItalyStrap Theme Framework)[https://github.com/ItalyStrap/italystrap]

> TODO https://inpsyde.com/en/remove-wordpress-hooks/

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

Copyright (c) 2019 Enea Overclokk, ItalyStrap

This code is licensed under the [MIT](LICENSE).

## Credits

### For the Event implementation

* (Carl Alexander)[https://carlalexander.ca/designing-system-wordpress-event-management/]
* (Carl Alexander)[https://carlalexander.ca/mediator-pattern-wordpress/]

### For the PsrDispatcher implementation

* (Symfony)[https://symfony.com/doc/current/components/event_dispatcher.html]
* (Larry Garfield)[https://github.com/Crell/Tukio]
* (Timothy Jacobs)[https://github.com/iron-bound-designs/psr-14-wp]