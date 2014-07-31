<?php

namespace PhpTry;

use PHPUnit_Framework_TestCase;

class LazyAttemptTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_call_the_callable_immediately()
    {
        $called = false;

        $attempt = Attempt::lazily(function() use (&$called) { $called = true; });

        $this->assertFalse($called);
    }
}
