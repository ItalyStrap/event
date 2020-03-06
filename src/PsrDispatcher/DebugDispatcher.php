<?php
declare(strict_types=1);

namespace ItalyStrap\PsrDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use function get_class;

/**
 * Class DebugEventDispatcher
 * @package ItalyStrap\Event\PsrDispatcher
 * @author Larry Garfield https://github.com/Crell/Tukio
 */
class DebugDispatcher implements EventDispatcherInterface {

	const M_DEBUG = 'Processing event of type {type}.';

	/**
	 * @var EventDispatcherInterface
	 */
	protected $dispatcher;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * DebugDispatcher constructor.
	 *
	 * @param EventDispatcherInterface $dispatcher The dispatcher to wrap and for which to log errors.
	 * @param LoggerInterface $logger The logger service through which to log.
	 */
	public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger) {
		$this->dispatcher = $dispatcher;
		$this->logger = $logger;
	}

	public function dispatch(object $event) {
		$this->logger->debug(self::M_DEBUG, ['type' => get_class($event), 'event' => $event]);
		return $this->dispatcher->dispatch($event);
	}
}
