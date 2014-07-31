<?php

namespace PhpTry;

use ArrayIterator;
use EmptyIterator;
use Exception;
use IteratorAggregate;
use UnexpectedValueException;

/**
 * The result of an Attempt
 *
 * @see Attempt
 */
abstract class Result implements IteratorAggregate
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
     * Returns this Result if Success, or the given Result otherwise.
     *
     * @param Result $try
     *
     * @return Result
     */
    public function orElse(Result $try)
    {
        return $this->isSuccess() ? $this : $try;
    }

    /**
     * Returns this Result if Success, or the result of the callable otherwise.
     *
     * @param callable $callable Callable returning a Result.
     *
     * @return Result
     */
    public function orElseCall($callable)
    {
        if ($this->isSuccess()) {
            return $this;
        }

        try {
            $value = call_user_func($callable);

            if ( ! $value instanceof Result) {
                return new Failure(new UnexpectedValueException('Return value of callable should be a Result.'));
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
     * @param callable $callable Callable returning a Result.
     *
     * @return Result
     */
    abstract public function flatMap($callable);

    /**
     * Maps the given function to the value from this Success, or returns this if this is a Failure.
     *
     * @param callable $callable Callable returning a value.
     *
     * @return Result
     */
    abstract public function map($callable);

    /**
     * Converts this to a Failure if the predicate is not satisfied.
     *
     * @param mixed $callable Callable retuning a boolean.
     *
     * @return Result
     */
    abstract public function filter($callable);

    /**
     * Applies the callable if this is a Failure, otherwise returns if this is a Success.
     *
     * Note: this is like `flatMap` for the exception.
     *
     * @param callable $callable Callable taking an exception and returning a Result.
     *
     * @return Result
     */
    abstract public function recoverWith($callable);

    /**
     * Applies the callable if this is a Failure, otherwise returns if this is a Success.
     *
     * Note: this is like `map` for the exception.
     *
     * @param callable $callable Callable taking an exception and returning a value.
     *
     * @return Result
     */
    abstract public function recover($callable);

    /**
     * Callable called when this is a Success.
     *
     * @param mixed $callable Callable taking a value.
     *
     * @return void
     */
    abstract public function ifSuccess($callable);

    /**
     * Callable called when this is a Success.
     *
     * @param mixed $callable Callable taking an exception.
     *
     * @return void
     */
    abstract public function ifFailure($callable);

    /**
     * Converts the Result to an Option.
     *
     * Returns 'Some' if it is Success, or 'None' if it's a Failure.
     *
     * @return \PhpOption\Option
     */
    abstract public function toOption();

    /**
     * Callable called when this is a Success.
     *
     * Like `map`, but without caring about the return value of the callable.
     * Useful for consuming the possible value of the Result.
     *
     * @param callable $callable Callable called regardless of the result type
     *
     * @return Result The current Result
     */
    abstract public function forAll($callable);
}
