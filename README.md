# Signal

Signal is a generic callback wrapper and proxy which enables custom resolution, including lazy dependency injection.

## What's the Problem?

I was looking at using `igorw/evenement` and saw this:

```php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

Lots of libraries support generic callbacks which are usually shown as anonymous functions.  This is great if you write a lot of custom wiring, not so great if you separate wiring/configuration data from wiring logic, e.g.:

FILE: `config.php`
```php
return [
	'events' => [
		'user.created' => 'UserCreatedHandler'
	]
]
```

Now when you read the config, you could do:

```php
foreach ($config['events'] as $event => $handler) {
	$emitter->on($event, $container->get($handler))
}
```

But then you're resolving all your handlers and their dependencies up front.  So maybe instead you do:

```php
foreach ($config['events'] as $event => $handler) {
	$emitter->on($event, function(...$params) use ($container, $handler) {
		$handler = $container->get($handler);
		return $handler(...$params);
	}
}
```

OK, that's not so bad, but now you're kind of assuming your handlers will always implement `__invoke`.  And what if you need to handle different callback styles elsewhere?  Now you have custom proxy callbacks all over.

## Is There a Better Way?

Yes!  **ONE CALLBACK TO RULE THEM ALL!**

```php
$signal = new Signal(function($signal) use ($container) {
	if (is_string($signal)) {
		if (function_exists($signal)) {
			return $signal;
		}

		if (strpos($signal, '::') !== FALSE) {
			list($class, $method) = explode('::', $signal);

			return [$container->get($class), $method];
		}

		if (class_exists($signal)) {
			return [$container->get($signal), '__invoke'];
		}
	}

	return NULL;
});
```

Then:

```php
foreach ($config['events'] as $event => $handler) {
	$emitter->on($event, $signal->create($handler))
}
```

Isn't that nice?

## Is that All?

No... because whatever `$handler` is gets passed to your custom resolver, you can do whatever you want when you resolve the handler.  For example, maybe you want to handle "artisan" style callbacks:

```php
$signal = str_replace('@', '::', $signal);
```

Maybe you want to create URL callbacks:

```php
if (preg_match('#^https?://#', $signal)) {
	$client = $container->get('APIClient');
	$client->setUrl($signal);

	return function() use ($client) {
		$client->setData(func_get_args());
		$client->send();
	};
}
```

Who the hell knows!

## OK, Aren't You Still Instantiating Everything Up Front?

No... `$signal->create($handler)` does not return the resolved handler.  Rather, it merely tracks the `$handler` and returns a proxy callback in its place, so the handler isn't resolved until it actually needs to be.  See the one test it has:

```php
<?php

use PHPUnit\Framework\TestCase;

use Hiraeth\Utils\Signal;

class SignalTest extends TestCase
{
	public function testProxy()
	{
		//
		// Create a new instance of signal with a totally useless
		// resolver that always returns the same callback.
		//

		$signal = new Signal(function($signal) {
			$this->assertSame($signal, 'fake_signal');

			return new class {
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

		//
		// Calling foobar resolves the signal to a target handler
		// our anonymous class above, and calls it to get the result.
		//

		$result = $foobar('foo', 'bar');

		$this->assertSame($result, 'foo bar');
	}
}
```

## Run The Test Yourself

```
php vendor/bin/phpunit --bootstrap vendor/autoload.php tests/SignalTest
```
