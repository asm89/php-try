<?php

namespace PhpTry;

use Exception;

/**
 * The Try type represents a computation that may have either resulted in an
 * exception, or returned a successfully computed value.
 *
 * This implementation is based on scala's Try, but the class is called Attempt
 * because "try" is a reserved keyword in PHP.
 *
 * @see https://github.com/scala/scala/blob/master/src/library/scala/util/Try.scala
 *
 * @note Success and Failure are no longer subclasses of Attempt. They now extend Result.
 */
abstract class Attempt
{
    /**
     * Constructs a Result by calling the passed callable.
     *
     * @param callable $callable
     * @param array    $arguments Optional arguments for the callable.
     *
     * @return Result
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
     * Constructs a LazyResult by calling the passed callable.
     *
     * The callable will only be called if a method on the Result is called.
     *
     * @param callable $callable
     * @param array    $arguments Optional arguments for the callable.
     *
     * @return LazyResult
     */
    public static function lazily($callable, $arguments = array())
    {
        return new LazyResult($callable, $arguments);
    }
}
