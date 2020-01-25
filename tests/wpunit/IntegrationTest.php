<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Event\SubscriberInterface;

/**
 * Class IntegrationTest
 * @package ItalyStrap\Tests
 */
class IntegrationTest extends WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	// Tests
	public function testItWorks() {

		$hooks = new \ItalyStrap\Event\Hooks();
		$manager = new \ItalyStrap\Event\EvenManager( $hooks );

		$subscriber = new class implements SubscriberInterface {

			/**
			 * @inheritDoc
			 */
			public function getSubscribedEvents(): array {
				return [
					'event_name'	=> 'method'
				];
			}

			public function method() {
				codecept_debug( \func_num_args() );
				codecept_debug( \func_get_args() );
				echo 'Ciao';
			}
		};

		$manager->add( $subscriber );

		$this->expectOutputString( 'Ciao' );
		$hooks->execute( 'event_name', 'value passed', 'other', 2, 3 );
	}
}
