# ItalyStrap Event API

[![Build Status](https://travis-ci.org/ItalyStrap/event.svg?branch=master)](https://travis-ci.org/ItalyStrap/event)
[![Latest Stable Version](https://img.shields.io/packagist/v/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Total Downloads](https://img.shields.io/packagist/dt/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
[![License](https://img.shields.io/packagist/l/italystrap/event.svg)](https://packagist.org/packages/italystrap/event)
![PHP from Packagist](https://img.shields.io/packagist/php-v/italystrap/event)

WordPress plus Psr-14 Event API the OOP way

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

The Hooks::class is a wrapper around the (WordPress Plugin API)[https://developer.wordpress.org/plugins/hooks/]

### Simple example for actions

```php
use ItalyStrap\Event\Hooks;

$hooks = new Hooks();
// Listen for `event_name`
$hooks->addListener( 'event_name', function () { echo 'Event Called'; }, 10 );

// This will echo 'Event Called' on `event_name`
$hooks->execute( 'event_name' );
```

### Simple example for filters

```php
use ItalyStrap\Event\Hooks;

$hooks = new Hooks();
// Listen for `event_name`
$hooks->addListener( 'event_name', function ( array $value ) {
    // Do your stuff here in the same ways you do with filters
    return $value;
}, 10 );

$value = [ 'some-key' => 'some-value' ];
// This will filters '$value' on `event_name`
$filtered_value = $hooks->filter( 'event_name', $value );
```

Ok, so, for now it is very straightforward, you will use it like you use the WordPress Plugin API but more OOP oriented,
you can inject the Hooks::class into yours classes.

```php
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\HooksInterface;

$hooks = new Hooks();

class MyClass {

    /**
     * @var HooksInterface 
     */
    private $hooks;
    public function __construct( HooksInterface $hooks ) {
        $this->hooks = $hooks;
    }

    public function doSomeStuffWithHooks() {
        // Do your stuff here with hooks
    }
}

$my_class = new MyClass( $hooks );
$my_class->doSomeStuffWithHooks();
```

What about the event manager?
Here a simple example:

```php
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\EventManager;
use ItalyStrap\Event\SubscriberInterface;

// Your class must implements the ItalyStrap\Event\SubscriberInterface
class MyClassSubscriber implements SubscriberInterface {
    public function getSubscribedEvents(): array {
        return ['event_name' => 'methodName'];
    }
    
    public function methodName(/* may be with some arguments if you use the ::filter() method */){
        // Do some stuff with hooks
    }
}

$subscriber = new MyClassSubscriber();

$hooks = new Hooks();
$event_manager = new EventManager( $hooks );
$event_manager->addSubscriber( $subscriber );

// It will execute the subscriber MyClassSubscriber::methodName
$hooks->execute( 'event_name' );
// or
$hooks->filter( 'event_name', $some_value );
```
A subscriber is a class that implements SubscriberInterface interface and could be the listener itself
passing a reference to the event/events and method/methods to execute.

The `ItalyStrap\Event\SubscriberInterface::getSubscribedEvents()` must return an array like those:

```php

return ['event_name' => 'method_name'];
return [
            'event_name' =>
            [
                KEYS::CALLBACK	=> 'method_name',
                KEYS::PRIORITY	=> $priority,
            ]
        ];
return [
           'event_name' =>
           [
               KEYS::CALLBACK	    => 'method_name',
               KEYS::PRIORITY	    => $priority,
               KEYS::ACCEPTED_ARGS	=> $accepted_args
           ]
       ];

```

In case the subscriber has a lot of events to subscribe to it is better to separate the busines logic in
another class an then use the subscriber to do the registration like this:

```php
use ItalyStrap\Event\Hooks;
use ItalyStrap\Event\EventManager;
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
            'event_name' => 'onEventName',
            'event_name2' => 'onEventName2',
            'event_name3' => 'onEventName3',
        ];
    }
    
    public function onEventName(/* may be with some arguments if you use the ::filter() method */){
        $this->logic->methodOne();
    }
    
    public function onEventName2(/* may be with some arguments if you use the ::filter() method */){
        $this->logic->methodTwo();
    }
    
    public function onEventName3(/* may be with some arguments if you use the ::filter() method */){
        $this->logic->methodThree();
    }
}
$logic = new MyBusinessLogic();
$subscriber = new MyClassSubscriber( $logic );

$hooks = new Hooks();
$event_manager = new EventManager( $hooks );
$event_manager->addSubscriber( $subscriber );

// It will execute the subscriber MyClassSubscriber::methodName
$hooks->execute( 'event_name' );
// or
$hooks->filter( 'event_name', $some_value );
```

## Advanced Usage

> TODO

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

Copyright (c) 2019 Enea Overclokk, ItalyStrap

This code is licensed under the [MIT](LICENSE).

## Credits

### For the Event implementation

* (Carl Alexander)[https://carlalexander.ca/designing-system-wordpress-event-management/]
* (Carl Alexander)[https://carlalexander.ca/mediator-pattern-wordpress/]

### For the EventDispatcher implementation

* (Symfony)[https://symfony.com/doc/current/components/event_dispatcher.html]
* (Larry Garfield)[https://github.com/Crell/Tukio]
* (Timothy Jacobs)[https://github.com/iron-bound-designs/psr-14-wp]