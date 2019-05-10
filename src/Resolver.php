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
	public function __construct(Hiraeth\Application $app)
	{
		$this->app = $app;
	}


	/**
	 *
	 */
	public function __invoke($signal)
	{
		$handler    = explode('::', $signal);
		$handler[0] = $this->app->get($handler[0]);
		$handler[1] = $handler[1] ?? '__invoke';

		return $handler;
	}
}
