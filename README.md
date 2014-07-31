Php Try type
============

A Try type for PHP.

The Try type is useful when called code will either return a value (Success) or
throw an exception (Failure). Instead of relying on the `try {} catch {}`
mechanism to handle this cases, the fact that code might either throw or return
a value is now encoded in its return type.

The type shows it usefulness with it's ability to create a "pipeline"
operations, catching exceptions along the way.

> Note: this implementation of the Try type is called Result, because "try" is
> a reserved keyword in PHP.

## Before / after

Before, the `UserService` and `Serializer` code might throw exceptions, so we
have an explicit try/catch:

```php
try {
    $user = $userService->findBy($id);
    $responseBody = $this->serializeUser($user);

    return new Response($user);
} catch (Exception $ex) {
    return Response('error', 500);
}
```

After, the `UserService` and `Serializer` now return a response of type `Result`
meaning that the computation will either be a `Failure` or a `Success`. The
combinators on the `Result` type are used to chain the following code in the case
the previous operation was successful.

```php
return $userService->findBy($id)
    ->flatMap(function($user) { return $this->serializeUser($user); }) // walk the happy path!
    ->map(function($responseBody) { return new Response($responseBody); })
    ->recover(function($ex) { return new Response('error', 500); })
    ->get(); // returns the wrapped value
```

## Installation

Run:

```
composer require phptry/phptry
```
or add it to your `composer.json` file.

## Usage

> Note: most of the example code below can be tried out with the
> `user-input.php` example from the `examples/` directory.

### Constructing an Result

Build a `Result` using the `Attempt::call()` factory, which invokes the callable immediately and handles the outcome,
either returning a `Success` containing the returned value, or a `Failure` containing the thrown exception.

```php
$result = \PhpTry\Attempt::call('callableThatMightThrow', array('argument1', 'argument2'));
```

Or use `Success` and `Failure` directly in your API instead of throwing exceptions:

```php
/**
 * @param mixed $dividend
 * @param mixed $divisor
 * @return \PhpTry\Result
 */
function divide($dividend, $divisor) {
    if ($divisor === 0) {
        return new \PhpTry\Failure(new InvalidArgumentException('Divisor cannot be 0.'));
    }

    return new \PhpTry\Success($dividend / $divisor);
}
```

### Using combinators on a Result

Now that we have the `Result` object we can use it's combinators to handle the
success and failure cases.

#### Getting the value

Gets the value from Success, or throws the original exception if it was a Failure.

```php
$result->get();
```

#### Falling back to a default value if Failure

Gets the value from Success, or get a provided alternative if the computation failed.

```php
// or a provided fallback value
$result->getOrElse(-1);

// or a value returned by the callable
// note: if the provided callable throws, this exception will not be catched
$result->getOrCall(function() { return -1; });

// or else return another Result
$result->orElse(Attempt::call('divide', array(42, 21)));

// or else return Another attempt from a callable
$result->orElseCall('promptDivide');
```

#### Walking the happy path

Sometimes you care about the Success path and want to propagate or even ignore
Failure. The `filter`, `flatMap` and `map` operators shown below will execute
the given code if the previous computation was a Success, or propagate the
Failure otherwise. If the function passed to `flatMap` or `map` throws, the
operation will result in a Failure.

```php
// map to Another attempt
$result->flatMap(function($elem) {
    return Attempt::call('divide', array($elem, promptDivide()->get()));
});

// map the success value to another value
$result->map(function($elem) { return $elem * 2; });

// Success, if the predicate holds for the Success value, Failure otherwise
$result->filter(function($elem) { return $elem === 42; })

// only foreachable if success
foreach ($result as $value) {
    echo $value;
}
```

#### Recovering from failure

When we do care about the Failure path we might want to try and fix things. The
`recover` and `recoverWith` operations are for Failure, what `flatMap` and
`map` are for Success.

```php
// recover with with a value returned by a callable
$result->recover(function($ex) { if ($ex instanceof RuntimeException) { return 21; } throw $ex; })

// recover with with an attempt returned by a callable
$result->recoverWith(function() { return promptDivide(); })
```

The `recover` and `recoverWith` combinators can be useful when calling for
example http services that might fail. A failed call can be recovered by
calling the service again or calling an alternative service.

#### Don't call us, we'll call you

The `Result` type can also call provided callables on a successful or failed computation:

```php
// if* handlers
$result
    ->ifSuccess(function($elem) { echo "Result of a / b * c is: $elem\n"; })
    ->ifFailure(function($elem) { echo "Something went wrong: " . $elem->getMessage() . "\n"; promptDivide(); })
;
```

#### Lazily executed Attempts

It is possible to execute the provided callable only when needed. This is
especially useful when recovering with for example expensive alternatives.

```php
$result->orElse(Attempt::lazily('someExpensiveComputationThatMightThrow'));
```

#### Other options

When you have [phpoption/phpoption] installed, the Result can be converted to
an Option. In this mapping a Succes maps to Some and a Failure maps to a None
value.

```php
$result->toOption(); // Some(value) or None()
```

[phpoption/phpoption]: https://github.com/schmittjoh/php-option

## Inspiration

- Implementation and general idea is based on scala's [Try]
- Schmittjoh's [Option type] for PHP

[Try]: http://www.scala-lang.org/api/2.9.3/scala/util/Try.html
[Option type]: https://github.com/schmittjoh/php-option
