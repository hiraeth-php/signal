<?php

namespace Hiraeth\Utils\Signal;

use Hiraeth;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as Container;

/**
 * Resolve <class> (implied __invoke) and <class>::<method> signals
 */
class Resolver
{
	/**
	 * A PSR-11 compatible container for class instantiation
	 *
	 * @var Container|null
	 */
	protected $container = NULL;


	/**
	 * Create a new instance of the resolver
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 * Resolve the signal into a callable
	 *
	 * @param string $signal The signal to resolve, in this case a callback string
	 * @return callable The callable method for when the signal is called
	 */
	public function __invoke(string $signal): callable
	{
		$handler    = explode('::', $signal);
		$handler[0] = $this->container->get($handler[0]);
		$handler[1] = $handler[1] ?? '__invoke';

		if (!is_callable($handler)) {
			throw new InvalidArgumentException(sprintf(
				'Could not convert signal "%s" to callable',
				$signal
			));
		}

		return $handler;
	}
}
