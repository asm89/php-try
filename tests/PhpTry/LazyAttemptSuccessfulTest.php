<?php

namespace PhpTry;

use PHPUnit_Framework_TestCase;

class LazyAttemptSuccessfulTest extends SuccessfulAttemptTestCase
{
    protected function createSuccess($value)
    {
        $callable = function() use ($value) { return $value; };

        return Attempt::lazily($callable);
    }
}
