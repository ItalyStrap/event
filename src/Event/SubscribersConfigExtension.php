<?php
declare(strict_types=1);

namespace ItalyStrap\Event;

use Auryn\ConfigException;
use Auryn\InjectionException;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Empress\AurynConfigInterface;
use ItalyStrap\Empress\Extension;
use ItalyStrap\Empress\Injector;

/**
 * Class EventResolverExtension
 * @package ItalyStrap\Event
 */
class SubscribersConfigExtension implements Extension {

	/** @var string */
	const SUBSCRIBERS = 'subscribers';

	/**
	 * @var SubscriberRegister
	 */
	private $event_manager;

	/**
	 * @var ConfigInterface
	 */
	private $config;

	/**
	 * EventResolverExtension constructor.
	 * @param SubscriberRegister $event_manager
	 * @param ConfigInterface $config
	 */
	public function __construct( SubscriberRegister $event_manager, ConfigInterface $config ) {
		$this->event_manager = $event_manager;
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function name(): string {
		return self::SUBSCRIBERS;
	}

	/**
	 * @inheritDoc
	 */
	public function execute( AurynConfigInterface $application ): void {
		$application->walk( $this->name(), [$this, 'walk'] );
	}

	/**
	 * @param string $class Array value from yous configuration
	 * @param int|string $index_or_optionName Array key from your configuration
	 * @param Injector $injector An instance of the Injector::class
	 * @throws ConfigException
	 * @throws InjectionException
	 */
	public function walk( string $class, $index_or_optionName, Injector $injector ): void {

		if ( \is_string( $index_or_optionName ) && empty( $this->config->get( $index_or_optionName, false ) ) ) {
			return;
		}

		$this->event_manager->addSubscriber( $injector->share( $class )->make( $class ) );
	}
}
