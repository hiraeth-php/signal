<?php

namespace Hiraeth\Utils\Signal;

use Hiraeth;

/**
 *
 */
class Resolver
{
	/**
	 *
	 */
	public function __construct(Hiraeth\Broker $broker)
	{
		$this->broker = $broker;
	}


	/**
	 *
	 */
	public function __invoke($signal)
	{
		$handler    = explode('::', $signal);
		$handler[0] = $this->broker->make($handler[0]);
		$handler[1] = $handler[1] ?? '__invoke';

		return $handler;
	}
}
