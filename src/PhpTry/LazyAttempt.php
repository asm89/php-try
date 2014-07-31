<?php

namespace PhpTry;

/**
 * Attempt that is evaluated on first access.
 *
 * @note Chaining should work as desired, but note that LazyAttempt methods do not return $this, but rather
 *       return an internal Attempt object.
 */
class LazyAttempt extends Attempt
{
    private $attempt;
    private $callable;
    private $arguments;

    public function __construct($callable, $arguments = array())
    {
        $this->callable  = $callable;
        $this->arguments = $arguments;
    }

    public function isFailure()
    {
        return $this->attempt()->isFailure();
    }

    public function isSuccess()
    {
        return $this->attempt()->isSuccess();
    }

    public function getOrElse($default)
    {
        return $this->attempt()->getOrElse($default);
    }

    public function getOrCall($callable)
    {
        return $this->attempt()->getOrCall($callable);
    }

    public function orElse(Attempt $try)
    {
        return $this->attempt()->orElse($try);
    }

    public function orElseCall($callable)
    {
        return $this->attempt()->orElseCall($callable);
    }

    public function getIterator()
    {
        return $this->attempt()->getIterator();
    }

    public function get()
    {
        return $this->attempt()->get();
    }

    public function flatMap($callable)
    {
        return $this->attempt()->flatMap($callable);
    }

    public function map($callable)
    {
        return $this->attempt()->map($callable);
    }

    public function filter($callable)
    {
        return $this->attempt()->filter($callable);
    }

    public function recoverWith($callable)
    {
        return $this->attempt()->recoverWith($callable);
    }

    public function recover($callable)
    {
        return $this->attempt()->recover($callable);
    }

    public function onSuccess($callable)
    {
        return $this->attempt()->onSuccess($callable);

    }

    public function onFailure($callable)
    {
        return $this->attempt()->onFailure($callable);
    }

    private function attempt()
    {
        if (null === $this->attempt) {
            return $this->attempt = Attempt::call($this->callable, $this->arguments);
        }

        return $this->attempt;
    }

    public function toOption()
    {
        return $this->attempt()->toOption();
    }

    public function forAll($callable)
    {
        return $this->attempt()->forAll($callable);
    }
}
