<?php

namespace PhpTry;

use Exception;
use PHPUnit_Framework_TestCase;

class AttemptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_Success_if_the_callable_does_not_throw()
    {
        $success = Attempt::call(function() { return 42; });

        $this->assertInstanceOf('PhpTry\Success', $success);
    }

    /**
     * @test
     */
    public function it_returns_Failure_if_the_callable_thrown()
    {
        $failure = Attempt::call(function() { throw new Exception(); });

        $this->assertInstanceOf('PhpTry\Failure', $failure);
    }
}
