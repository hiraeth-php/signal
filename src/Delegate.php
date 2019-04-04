<?php

namespace Hiraeth\Utils\Signal;

use Hiraeth;

/**
 * A delegate to create a new instance of Signal
 */
class Delegate implements Hiraeth\Delegate
{
	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	static public function getClass(): string
	{
		return Hiraeth\Utils\Signal::class;
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Application $app The application instance for which the delegate operates
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$signal = new Hiraeth\Utils\Signal($app->get(ResolverInterface::class));

		return $app->share($signal);
	}
}
