<?php

namespace Hiraeth\Utils\Signal;

use Hiraeth;

/**
 * {@inheritDoc}
 */
class SignalDelegate implements Hiraeth\Delegate
{
	/**
	 * {@inheritDoc}
	 */
	static public function getClass(): string
	{
		return Hiraeth\Utils\Signal::class;
	}


	/**
	 * {@inheritDoc}
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$signal = new Hiraeth\Utils\Signal($app->get(
			$app->getConfig('packages/signal', 'signal.resolver', Resolver::class)
		));

		return $app->share($signal);
	}
}
