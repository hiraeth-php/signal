<?php

namespace Hiraeth\Utils;

use Closure;

/**
 * A generic callback wrapper which enables lazy DI resolution
 */
class Signal
{
	/**
	 * The callable responsible for resolving signals into valid callables
	 *
	 * @access protected
	 * @var callable
	 */
	protected $resolver = NULL;


	/**
	 * An array of registered signals (potential callbacks to resolve)
	 *
	 * @access protected
	 * @var array
	 */
	protected $signals = array();


	/**
	 * An array of resolved signals
	 *
	 * @access protected
	 * @var array
	 */
	protected $targets = array();


	/**
 	 * Create a new instance
	 *
	 * @access public
	 * @param callable $resovler The resolver to resolve signals to callable targets
	 * @return void
	 */
	public function __construct(callable $resolver)
	{
		$this->resolver = $resolver;
	}


	/**
	 * Track a signal and create a proxy to take its place
	 *
	 * @access public
	 * @param mixed $signal The signal to be resolved by the registered resolver
	 * @return callable A proxy for or the resolved target handler
	 */
	public function create($signal): callable
	{
		$signal_number = array_search($signal, $this->signals);

		if ($signal_number !== FALSE && isset($this->targets[$signal_number])) {
			return $this->targets[$signal_number];
		}

		$this->signals[$signal_number = count($this->signals)] = $signal;

		return function(...$params) use ($signal_number) {
			return $this->proxy($signal_number, ...$params);
		};
	}


	/**
 	 * Proxy a call to a registered signal, resolving if necessary
	 *
	 * @access protected
	 * @param integer $signal_number The signal number of the signal to proxy to and resolve
	 * @param mixed $params ...the parameters as passed to the proxy callback
	 * @return mixed The return from the signal's resolved target
	 */
	protected function proxy($signal_number, ...$params)
	{
		$this->targets[$signal_number] = ($this->resolver)($this->signals[$signal_number]);

		if (!is_callable($this->targets[$signal_number])) {
			throw new RuntimeException(sprintf(
				'Resolver failed to convert signal "%s" to callable target.',
				print_r($this->signals[$signal_number], TRUE)
			));
		}

		return $this->targets[$signal_number](...$params);
	}
}