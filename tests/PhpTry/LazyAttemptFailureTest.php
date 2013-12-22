<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

class LazyAttemptFailureTest extends FailedAttemptTestCase
{
    protected function createFailure($exception)
    {
        $callable = function() use ($exception) { throw $exception; };

        return Attempt::lazily($callable);
    }
}
