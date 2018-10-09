<?php

class SignalTest extends PHPUnit\Framework\TestCase
{
	public function testProxy()
	{
		//
		// Create a new instance of signal with a totally useless
		// resolver that always returns the same callback.
		//

		$signal = new Hiraeth\Utils\Signal(function($signal) use (&$target) {
			$this->assertSame($signal, 'fake_signal');

			return $target = new class {
				public function __invoke($foo, $bar)
				{
					return $foo . ' ' . $bar;
				}
			};
		});

		//
		// Create a new proxy for the signal as $foobar
		// this does not yet call the resolver, rather $foobar
		// contains a callback that will call the resolver when
		// it gets called.
		//

		$foobar = $signal->create('fake_signal');

		$this->assertNotSame($target, $foobar);

		//
		// Calling foobar resolves the signal to a target handler
		// our anonymous class above, and calls it to get the result.
		//

		$result = $foobar('foo', 'bar');

		$this->assertSame($result, 'foo bar');

		//
		// Once resolved, calling $signal->create() will return the
		// resolved handler directly without a proxy.
		//

		$newbar = $signal->create('fake_signal');

		$this->assertSame($target, $newbar);
	}
}
