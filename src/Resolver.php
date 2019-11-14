<?php

namespace Hiraeth\Utils\Signal;

use Hiraeth;
use Psr\Container\ContainerInterface as Container;

/**
 *
 */
class Resolver
{
	/**
	 *
	 */
	protected $container = NULL;

	/**
	 *
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 *
	 */
	public function __invoke($signal): callable
	{
		$handler    = explode('::', $signal);
		$handler[0] = $this->container->get($handler[0]);
		$handler[1] = $handler[1] ?? '__invoke';

		return $handler;
	}
}
