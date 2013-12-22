Php Try type
============

A Try type for PHP.

The Try type is useful when called code will either return a value (Success) or
throw an exception (Failure). Instead of relying on the `try {} catch {}`
mechanism to handle this cases, the fact that code might either throw or return
a value is now encoded in its return type.

The type shows it usefulness with it's ability to create a "pipeline"
operations, catching exceptions along the way.

> Note: this implementation of the Try type is called Attempt, because "try" is
> a reserved keyword in PHP.

## Installation

Run:

```
composer require phptry/phptry
```
or add it to your `composer.json` file.

## Usage

> Note: most of the example code below can be tried out with the
> `user-input.php` example from the `examples/` directory.

### Constructing an Attempt

Turn any callable in an `Attempt` using the `Attempt::call()` construct it.

```php
\PhpTry\Attempt::call('callableThatMightThrow', array('argument1', 'argument2'));
```

Or use `Success` and `Failure` directly in your API instead of throwing exceptions:

```php
function divide($dividend, $divisor) {
    if ($divisor === 0) {
        return new \PhpTry\Failure(new InvalidArgumentException('Divisor cannot be 0.'));
    }

    return new \PhpTry\Success($dividend / $divisor);
}
```

### Using combinators on an Attempt

Now that we have the `Attempt` object we can use it's combinators to handle the
success and failure cases.

#### Getting the value

Gets the value from Success, or throws the original exception if it was a Failure.

```
echo $try->get();
```

#### Falling back to a default value if Failure

Gets the value from Success, or get a provided alternative if the computation failed.

```php
// or a provided fallback value
echo $try->getOrElse(-1);

// or a value returned by the callable
// note: if the provided callable throws, this exception will not be catched
echo $try->getOrCall(function() { return -1; });

// or else return another Attempt
echo $try->orElse(Attempt::call('divide', array(42, 21)));

// or else return another attempt from a callable
echo $try->orElseCall('promptDivide')->get();
```

#### Walking the happy path

Sometimes you care about the Success path and want to propagate or even ignore
Failure. The `filter`, `flatMap` and `map` operators shown below will execute
the given code if the previous computation was a Success, or propagate the
Failure otherwise. If the function passed to `flatMap` or `map` throws, the
operation will result in a Failure.

```php
// map with another attempt
$try->flatMap(function($elem) {
    return Attempt::call('divide', array($elem, promptDivide()->get()));
});

// map the success value to another value
$try->map(function($elem) { return $elem * 2; });

// Success, if the predicate holds for the Success value, Failure otherwise
$try->filter(function($elem) { return $elem === 42; })

// only foreachable if success
foreach ($try as $result) {
    echo $result;
}
```

#### Recovering from failure

When we do care about the Failure path we might want to try and fix things. The
`recover` and `recoverWith` operations are for Failure, what `flatMap` and
`map` are for Success.

```php
// recover with with a value returned by a callable
$try->recover(function($ex) { if ($ex instanceof RuntimeException) { return 21; } throw $ex; })

// recover with with an attempt returned by a callable
$try->recoverWith(function() { return promptDivide(); })
```

The `recover` and `recoverWith` combinators can be useful when calling for
example http services that might fail. A failed call can be recovered by
calling the service again or calling an alternative service.

#### Don't call us, we'll call you

The Try type can also call provided callables on a successful or failed computation:

```php
// on* handlers
$try
    ->onSuccess(function($elem) { echo "Result of a / b * c is: $elem\n"; })
    ->onFailure(function($elem) { echo "Something went wrong: " . $elem->getMessage() . "\n"; promptDivide(); })
;
```

#### Lazily executed Attempts

It is possible to execute the provided callable only when needed. This is
especially useful when recovering with for example expensive alternatives.

```
$try
    ->orElse(Attempt::lazily('someExpensiveComputationThatMightThrow'));
```

#### Other options

When you have [phpoption/phpoptions] installed, the Attempt can be converted to
an Option. In this mapping a Succes maps to Some and a Failure maps to a None
value.

```
$try->toOption(); // Some(value) or None()
```

[phpoption/phpoption]: https://github.com/schmittjoh/php-option

## Inspiration

- Implementation and general idea is based on scala's [Try]
- Schmittjoh's [Option type] for PHP

[Try]: http://www.scala-lang.org/api/2.9.3/scala/util/Try.html
[Option type]: https://github.com/schmittjoh/php-option
