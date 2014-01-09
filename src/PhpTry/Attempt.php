<?php

namespace PhpTry;

use ArrayIterator;
use EmptyIterator;
use Exception;
use IteratorAggregate;
use UnexpectedValueException;

/**
 * The Try type represents a computation that may either result in an
 * exception, or return a successfully computed value.
 *
 * This implementation is based on scala's Try, but the class is called Attempt 
 * because "try" is a reserved keyword in PHP.
 *
 * @see https://github.com/scala/scala/blob/master/src/library/scala/util/Try.scala
 */
abstract class Attempt implements IteratorAggregate
{
    /**
     * @return boolean True, if this is Failure, false otherwise
     */
    abstract public function isFailure();

    /**
     * @return boolean True, if this is Success, false otherwise
     */
    abstract public function isSuccess();

    /**
     * Returns the value if it is Success, or the default argument otherwise.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOrElse($default)
    {
        return $this->isSuccess() ? $this->get() : $default;
    }

    /**
     * Returns the value if it is Success, or the the return value of the callable otherwise.
     *
     * Note: will throw if the callable throws.
     *
     * @param callable $callable
     *
     * @return mixed
     */
    public function getOrCall($callable)
    {
        return $this->isSuccess() ? $this->get() : call_user_func($callable);
    }

    /**
     * Returns this Attempt if Success, or the given Attempt otherwise.
     *
     * @param Attempt $try
     *
     * @return Attempt
     */
    public function orElse(Attempt $try)
    {
        return $this->isSuccess() ? $this : $try;
    }

    /**
     * Returns this Attempt if Success, or the result of the callable otherwise.
     *
     * @param callable $callable Callable returning an Attempt.
     *
     * @return Attempt
     */
    public function orElseCall($callable)
    {
        if ($this->isSuccess()) {
            return $this;
        }

        try {
            $value = call_user_func($callable);

            if ( ! $value instanceof Attempt) {
                return new Failure(new UnexpectedValueException('Return value of callable should be an Attempt.'));
            }

            return $value;
        } catch (Exception $ex) {
            return new Failure($ex);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        if ($this->isSuccess()) {
            return new ArrayIterator(array($this->get()));
        } else {
            return new EmptyIterator();
        }
    }

    /**
     * Its value if Success, or throws the exception if this is a Failure.
     *
     * @return mixed
     */
    abstract public function get();


    /**
     * Returns the given function applied to the value from this Success, or returns this if this is a Failure.
     *
     * Useful for calling Attempting another operation that might throw an
     * exception, while an already catched exception gets propagated.
     *
     * @param callable $callable Callable returning an Attempt.
     *
     * @return Attempt
     */
    abstract public function flatMap($callable);

    /**
     * Maps the given function to the value from this Success, or returns this if this is a Failure.
     *
     * @param callable $callable Callable returning a value.
     *
     * @return Attempt
     */
    abstract public function map($callable);

    /**
     * Converts this to a Failure if the predicate is not satisfied.
     *
     * @param mixed $callable Callable retuning a boolean.
     *
     * @return Attempt
     */
    abstract public function filter($callable);

    /**
     * Applies the callable if this is a Failure, otherwise returns if this is a Success.
     *
     * Note: this is like `flatMap` for the exception.
     *
     * @param callable $callable Callable taking an exception and returning an Attempt.
     *
     * @return Attempt
     */
    abstract public function recoverWith($callable);

    /**
     * Applies the callable if this is a Failure, otherwise returns if this is a Success.
     *
     * Note: this is like `map` for the exception.
     *
     * @param callable $callable Callable taking an exception and returning a value.
     *
     * @return Attempt
     */
    abstract public function recover($callable);

    /**
     * Callable called when this is a Success.
     *
     * @param mixed $callable Callable taking a value.
     *
     * @return void
     */
    abstract public function onSuccess($callable);

    /**
     * Callable called when this is a Success.
     *
     * @param mixed $callable Callable taking an exception.
     *
     * @return void
     */
    abstract public function onFailure($callable);

    /**
     * Converts the Attempt to an Option.
     *
     * Returns 'Some' if it is Success, or 'None' if it's a Failure.
     *
     * @return \PhpOption\Option
     */
    abstract public function toOption();

    /**
     * Constructs an Attempt by calling the passed callable.
     *
     * @param callable $callable
     * @param array    $arguments Optional arguments for the callable.
     *
     * @return Attempt
     */
    public static function call($callable, $arguments = array())
    {
        try {
            return new Success(call_user_func_array($callable, $arguments));
        } catch (Exception $e) {
            return new Failure($e);
        }
    }

    /**
     * Constructs a LazyAttempt by calling the passed callable.
     *
     * The callable will only be called if a method on the Attempt is called.
     *
     * @param callable $callable
     * @param array    $arguments Optional arguments for the callable.
     *
     * @return LazyAttempt
     */
    public static function lazily($callable, $arguments = array())
    {
        return new LazyAttempt($callable, $arguments);
    }
}
