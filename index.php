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

if ( ! \function_exists( 'd_footer' ) ) {
	function d_footer( ...$args ) {
		\add_action( 'shutdown', function () use ( $args ) {
			d( ...$args );
		} );
	}
}

add_action( 'plugins_loaded', function () {
	require( __DIR__ . '/vendor/autoload.php' );
//	require 'example.php';
} );
