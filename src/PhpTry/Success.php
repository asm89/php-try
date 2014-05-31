<?php

namespace PhpTry;

use Exception;
use RuntimeException;
use UnexpectedValueException;

class Success extends Attempt
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isFailure()
    {
        return false;
    }

    public function isSuccess()
    {
        return true;
    }

    public function get()
    {
        return $this->value;
    }

    public function flatMap($callable)
    {
        try {
            $value = call_user_func_array($callable, array($this->value));

            if ( ! $value instanceof Attempt) {
                return new Failure(new UnexpectedValueException('Return value of callable should be an Attempt.'));
            }

            return $value;
        } catch (Exception $ex) {
            return new Failure($ex);
        }
    }

    public function map($callable)
    {
        return Attempt::call($callable, array($this->value));
    }

    public function filter($callable)
    {
        try {
            $value = call_user_func_array($callable, array($this->value));

            if ($value) {
                return $this;
            }

            return new Failure(new NoSuchElementException('Predicate does not hold for ' . $this->value));
        } catch (Exception $ex) {
            return new Failure($ex);
        }
    }

    public function recoverWith($callable)
    {
        return $this;
    }

    public function recover($callable)
    {
        return $this;
    }

    public function onFailure($callable)
    {
        return $this;
    }

    public function onSuccess($callable)
    {
        $value = call_user_func_array($callable, array($this->value));

        return $this;
    }

    public function toOption()
    {
        return new \PhpOption\Some($this->value);
    }

    public function forAll($callable)
    {
        call_user_func($callable, $this->value);

        return $this;
    }
}
