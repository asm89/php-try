<?php

namespace PhpTry;

use Exception;
use UnexpectedValueException;

class Failure extends Result
{
    private $exception;

    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    public function isFailure()
    {
        return true;
    }

    public function isSuccess()
    {
        return false;
    }

    public function get()
    {
        throw $this->exception;
    }

    public function flatMap($callable)
    {
        return $this;
    }

    public function map($callable)
    {
        return $this;
    }

    public function filter($callable)
    {
        return $this;
    }

    public function recoverWith($callable)
    {
        try {
            $value = call_user_func_array($callable, array($this->exception));

            if ( ! $value instanceof Result) {
                return new Failure(new UnexpectedValueException('Return value of callable should be a Result.'));
            }

            return $value;
        } catch (Exception $ex) {
            return new Failure($ex);
        }
    }

    public function recover($callable)
    {
        return Attempt::call($callable, array($this->exception));
    }

    public function ifFailure($callable)
    {
        call_user_func_array($callable, array($this->exception));

        return $this;
    }

    public function ifSuccess($callable)
    {
        return $this;
    }

    public function forAll($callable)
    {
        return $this;
    }

    public function toOption()
    {
        return \PhpOption\None::create();
    }
}
