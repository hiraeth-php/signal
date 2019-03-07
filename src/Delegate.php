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
	 * Construct the delegate
	 *
	 * @access public
	 * @param Hiraeth\Application $app An application instance
	 * @return void
	 */
	public function __construct(Hiraeth\Application $app)
	{
		$this->app = $app;
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Broker $broker The dependency injector instance
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Broker $broker): object
	{
		$signal = new Hiraeth\Utils\Signal($broker->make('Hiraeth\Utils\Signal\Resolver'));

		$broker->share($signal);

		return $signal;
	}
}
