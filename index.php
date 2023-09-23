<?php
/*
Plugin Name: Event
Description: Event Handler for WordPress
Plugin URI: https://italystrap.com
Author: Enea Overclokk
Author URI: https://italystrap.com
Version: 1.0.0
License: GPL2
Text Domain: Text Domain
Domain Path: Domain Path
*/

/*

    Copyright (C) Year  Enea Overclokk  Email

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('plugins_loaded', function () {
	require __DIR__ . '/vendor/autoload.php';

    $listenerProvider = new \ItalyStrap\Event\GlobalOrderedListenerProvider();
    $state = new \ItalyStrap\Event\GlobalState();

    $event = new \stdClass();

    $listenerProvider->addListener(\stdClass::class, function (object $event) {
        $event->name = 'Hello';
    }, 10);

    $listener = new class ($state) {
        private $state;

        public function __construct(\ItalyStrap\Event\GlobalState $state) {
            $this->state = $state;
        }
        public function __invoke(object $event) {
            $event->name .= ' World';
            $event->currentState = $this->state->currentEventName();
        }
    };

    $listenerProvider->addListener(\stdClass::class, $listener, 20);

    $dispatcher = new \ItalyStrap\Event\Dispatcher($listenerProvider, $state);

    $name = $dispatcher->dispatch($event)->name;
});
