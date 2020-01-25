<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

/**
 * Class IntegrationTest
 * @package ItalyStrap\Tests
 */
class IntegrationTest extends \Codeception\TestCase\WPTestCase {

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
		$manager = new \ItalyStrap\Event\EvenManager( new \ItalyStrap\Event\Hooks() );

		$manager->add();
	}
}
